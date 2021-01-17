<?php


namespace cybox\cbxcore\db;


use cybox\cbxcore\Application;

class Database
{
    public \PDO $pdo;

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR . '/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration){
            if ($migration === '.' || $migration === '..'){
                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);

            $instance = new $className;
            $this->log("Applying migration $migration");
            $instance->up();
            $this->log("Applied migration $migration");

            $newMigrations[] = $migration;
        }

        // Todo: dit gaan we anders doen, per migratie comitten voor het geval een migratie een fatal error geeft
        if (!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
            $this->log("All migrations are applied");
        }

    }

    private function createMigrationsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ) ENGINE=INNODB;)");
    }

    private function getAppliedMigrations(): array
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations): void
    {
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));

        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) 
            VALUES $str
            ");
        $statement->execute();

    }

    public function prepare(string $sql): bool|\PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    public function log(string $message): void
    {
        echo '[' . date('d-m-Y H:i:s') . '] - ' . $message . PHP_EOL;
    }
}