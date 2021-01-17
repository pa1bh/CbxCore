<?php

namespace cybox\cbxcore;

use cybox\cbxcore\db\Database;
use cybox\cbxcore\db\DbModal;

/**
 * Class Application
 * @package cybox\cbxcore
 */
class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

    public static string $ROOT_DIR;
    public string $layout = 'main';
    // Todo: learn about typed property php > 7.4
    public Router $router;
    public Request $request;
    public Response $response;
    public ?Controller $controller;
    public Session $session;
    public Database $db;
    public ?UserModel $user;
    public View $view;
    //public string $user;
    /**
     * @var mixed
     */
    private $userClass;


    public function __construct(string $rootPath, array $config)
    {
        $this->userClass = $config['userClass'];

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->controller = new Controller();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }else{
            $this->user = null;
        }
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    Public static Application $app;

    public function run(): void
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);
        try{
            echo $this->router->resolve();
        }catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
            //Debug::dump($e);
        }
    }

    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(): bool
    {
        return !self::$app->user;
    }

    public function on (string $eventName, callable $callback): void
    {
        $this->eventListeners[$eventName][] =  $callback;
    }

    private function triggerEvent(string $eventName): void
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach ($callbacks as $callback){
            call_user_func($callback);
        }
    }

}