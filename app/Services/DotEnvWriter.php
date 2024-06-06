<?php
/**
 * Original: MirazMac\DotEnv\Writer
 * https://github.com/MirazMac/DotEnvWriter
 *
 * A PHP library to write values to .env files.
 *
 * Heavily inspired from https://github.com/msztorc/laravel-env
 *
 * @author Miraz Mac <mirazmac@gmail.com>
 * @link https://mirazmac.com
 */
namespace IXP\Services;

use InvalidArgumentException;
use LogicException;

class DotEnvWriter
{
    /**
     * The .env file content
     *
     * @var        string
     */
    protected string $content = "";

    /**
     * Path to the .env file
     *
     * @var        string|null
     */
    protected ?string $sourceFile = null;

    /**
     * Parsed variables, just for reference, not properly type-casted
     *
     * @var        array
     */
    protected array $variables = [];

    /**
     * Stores if a change was made todo: Disposable
     *
     * @var        bool
     */
    protected bool $changed = false;

    /**
     * Constructs a new instance.
     *
     * @param      string|null      $sourceFile  The environment path
     * @throws     LogicException  If the file is missing
     */
    public function __construct(?string $sourceFile = null)
    {
        if (null !== $sourceFile) {
            $this->sourceFile = $sourceFile;
            $this->content = file_get_contents($sourceFile);
            $this->parse();
        }
    }

    /**
     * Set the value of an environment variable, updated if exists, added if doesn't todo: Refactor
     *
     * @param      string  $key         The key
     * @param      string  $value       The value
     * @param      bool    $forceQuote  By default the whether the value is wrapped
     *                                  in double quotes is determined automatically.
     *                                  However, you may wish to force quote a value
     *
     * @throws     InvalidArgumentException If a new key contains invalid characters
     * @return     self
     */
    public function set(string $key, string $value, bool $forceQuote = false) : self
    {
        $originalValue = $value;

        // Quote properly
        $value = $this->escapeValue($value, $forceQuote);

        // If the key exists, replace it's value
        if ($this->exists($key)) {
            $this->content = preg_replace("/^{$key}=.*$/mu", "{$key}={$value}", $this->content);
        } else {
            // otherwise append to the end
            if (!$this->isValidName($key)) {
                throw new InvalidArgumentException("Failed to add new key `{$key}`. As it contains invalid characters, please use only ASCII letters, digits and underscores only.");
            }

            $this->content .= "{$key}={$value}" . PHP_EOL;
        }

        $this->variables[$key] = $originalValue;
        $this->changed = true;

        return $this;
    }

    /**
     * Set more values at once, downside of this is you can't set "forceQuote" specificly todo: Disposable
     *
     * @param      array  $values  The values as key => value pairs
     * @return     self
     */
    public function setValues(array $values) : self
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Delete an environment variable if present todo: Refactor
     *
     * @param      string  $key    The key
     * @return     self
     */
    public function delete(string $key) : self
    {
        if ($this->exists($key)) {
            $this->content = preg_replace("/^{$key}=.*\s{0,1}/mu", '', $this->content);
            unset($this->variables[$key]);
            $this->changed = true;
        }

        return $this;
    }

    /**
     * Remark an environment variable if present
     *
     * @param      string  $key    The key
     * @return     self
     */
    public function disable(string $key) : self
    {
        $lineId = $this->findVariable( $key );

        if ($lineId !== false) {
            if(!$this->variables[$lineId]["changed"]) {
                $this->variables[$lineId]["status"] = false;
                $this->variables[$lineId]["changed"] = true;
            }
        }

        return $this;
    }

    /**
     * Unremarked an environment variable if present
     *
     * @param      string  $key    The key
     * @return     self
     */
    public function enable(string $key) : self
    {
        $lineId = $this->findVariable( $key );

        if ($lineId !== false) {
            if($this->variables[$lineId]["changed"]) {
                $this->variables[$lineId]["status"] = true;
                $this->variables[$lineId]["changed"] = true;
            }
        }

        return $this;
    }

    /**
     * States if one or more values has changed todo: Disposable
     *
     * @return     bool
     */
    public function hasChanged() : bool
    {
        return $this->changed;
    }

    /**
     * Returns the value for a variable is present todo: Refactor
     *
     * NOTE: This is a writer library so all values are parsed as string.
     * Don't use this as an way to read values from dot env files. Instead use something robust like:
     * https://github.com/vlucas/phpdotenv
     *
     * @param      string  $key       The key
     * @return     string
     */
    public function get(string $key): string
    {
        return $this->exists($key) ? $this->variables[$key] : '';
    }

    /**
     * Returns all the variables parsed
     *
     * @return     array
     */
    public function getAll(): array
    {
        return $this->variables;
    }

    /**
     * Returns the current content
     *
     * @return     string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * Write the contents to the env file todo: Refactor
     *
     * @param      bool  $force     By default we only write when something has changed,
     *                              but you can force to write the file
     * @param      string $destFile Destionation file. By default it's the same as $sourceFile is provided
     *
     * @return     bool
     */
    public function write(bool $force = false, ?string $destFile = null) : bool
    {
        if (null === $destFile) {
            $destFile = $this->sourceFile;
        }

        if (null === $destFile) {
            throw new LogicException("No file provided");
        }

        // If nothing is changed don't bother writing unless forced
        if (!$this->hasChanged() && !$force) {
            return true;
        }

        return (false !== file_put_contents($destFile, $this->content, \LOCK_EX)  ?? true);
    }

    /**
     * Check if a variable exists or not todo: Refactor
     *
     * @param      string  $key    The key
     * @return     bool
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->variables);
    }

    /**
     * Find a variable line id in the $variables array
     *
     * @param string $key
     * @return false|int
     */
    protected function findVariable( string $key): false|int
    {
        return array_search( $key, array_column( $this->variables, 'key' ) );
    }

    /**
     * Determines whether the specified key is valid name for .env files.
     *
     * @param      string  $key    The key
     *
     * @return     bool
     */
    protected function isValidName(string $key) : bool
    {
        return (bool)preg_match( '/^[\w\.]+$/', $key );
    }

    /**
     * Parses the environment file line by line and store the variables
     */
    protected function parse() : void
    {
        $lines = preg_split('/\r\n|\r|\n/', $this->content);

        foreach ($lines as $index => $line) {
            if (mb_strlen(trim($line)) && !(mb_strpos(trim($line), '#') === 0)) {
                [$key, $value] = explode('=', (string) $line);
                $this->variables[] = [
                    "key" => $key,
                    "value" => $this->formatValue($value),
                    "status" => true,
                    "changed" => false,
                ];
            } else {
                $unremarkedLine = trim(substr((string) $line, 1));
                $validVariable = preg_match("/^([A-Z_]+)=(.+)$/", $unremarkedLine, $matches);
                if ($validVariable) {
                    $this->variables[] = [
                        "key" => $matches[1],
                        "value" => $this->formatValue($matches[2]),
                        "status" => false,
                        "changed" => false,
                    ];
                } else {
                    $this->variables[] = [
                        "key" => null,
                        "value" => $line,
                        "status" => false,
                        "changed" => false,
                    ];
                }
            }
        }
    }

    /**
     * Strips quotes from the values when reading
     *
     * @param      string  $value  The value
     * @return     string
     */
    protected function stripQuotes(string $value): string
    {
        return preg_replace('/^(\'(.*)\'|"(.*)")$/u', '$2$3', $value);
    }

    /**
     * Formats the value for human friendly output
     *
     * @param      string  $value  The value
     * @return     string
     */
    protected function formatValue(string $value): string
    {
        $value = trim(explode('#', trim($value))[0]);

        return stripslashes($this->stripQuotes($value));
    }

    /**
     * Escapes the value before writing to the contents
     *
     * @param      string  $value       The value
     * @param      bool    $forceQuote  Whether force quoting is preferred
     * @return     string
     */
    protected function escapeValue(string $value, bool $forceQuote): string
    {
        if ('' === $value) {
            return '';
        }

        // Quote the values if
        // it contains white-space or the following characters: " \ = : . $ ( )
        // or simply force quote is enabled
        if (preg_match('/\s|"|\\\\|=|:|\.|\$|\(|\)/u', $value) || $forceQuote) {
            // Replace backslashes with even more backslashes so when writing we can have escaped backslashes
            // damn.. that rhymes
            $value = str_replace('\\', '\\\\\\\\', $value);
            // Wrap the
            $value = '"' . addcslashes($value, '"') . '"';
        }

        return $value;
    }
}