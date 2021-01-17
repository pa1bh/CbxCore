<?php


namespace cybox\cbxcore;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Model
 * @package cybox\cbxcore
 */
abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public array $errors = [];

    abstract public function rules(): array;
    public function labels(): array
    {
        return [];
    }

    public function getLabel($attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function loadData(array $data): void
    {
        foreach ($data as $key => $value){
            if (property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules){
            $value = $this->{$attribute};
            foreach ($rules as $rule){
                $ruleName = $rule;
                if (!is_string($ruleName)){
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }

                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']){
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }

                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }

                if ($ruleName === self::RULE_UNIQUE){
                    $className = $rule['class'];

                    // attribute kan ook opgegeven worden in rule config
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();

                    if ($record){
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]) ;
                    }
                }




            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(int|string $attribute, string $RULE_REQUIRED, array $params = []): void
    {
        $message = $this->errorMessages()[$RULE_REQUIRED] ?? '';
        foreach ($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function addError(int|string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }



    #[ArrayShape([
        self::RULE_REQUIRED => "string",
        self::RULE_EMAIL => "string",
        self::RULE_MIN => "string",
        self::RULE_MAX => "string",
        self::RULE_MATCH => "string"])]
    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email adress',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'This field {field} must be unique',
        ];
    }

    public function hasError(string $attribute): bool|array
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError(string|null $attribute): string|bool
    {
        return $this->errors[$attribute][0] ?? false;
    }
}