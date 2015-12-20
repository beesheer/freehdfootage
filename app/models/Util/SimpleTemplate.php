<?php
/**
 * Util_SimpleTemplate
 *
 * Simple templating helper class.
 * (1) Pass in a string with token holders.
 * (2) Assign tokens and render the translated string.
 *
 * Example of Usage:
 *
    $templateOptions = array(
        'tokens' => array(
            'firstName' => 'Bin',
            'lastName' => 'Xu'
        ),
        'templateString' => '{firstName} {lastName} lives in Canada {testToken}',
        'removeTokenHolderIfEmpty' => false
    );
    $template = new Util_SimpleTemplate($templateOptions);
    print $template->render();
 *
 */
class Util_SimpleTemplate
{
    /**
     * Token holder prefix character.
     *
     * @var string
     */
    protected $_tokenHolderPrefix = '{';

    /**
     * Token holder prefix character.
     *
     * @var string
     */
    protected $_tokenHolderPostfix = '}';

    /**
     * The template string with tokens.
     *
     * @var string
     */
    protected $_templateString = '';

    /**
     * The list of tokens currently available.
     *
     * @var array
     */
    protected $_tokens = '';

    /**
     * Remove the token holder if no token value exists in the token array.
     *
     * @var boolean
     */
    protected $_removeTokenHolderIfEmpty = true;

    /**
     * Construct function.
     *
     * @param array $options
     * @return Util_SimpleTemplate
     */
    public function __construct($options = array())
    {
        $this->init($options);
    }

    /**
     * Init with passed in options.
     *
     * @param array $options
     * @return Ext_SimpleTemplate
     */
    public function init($options = array())
    {
        if (!is_array($options) && empty($options)) {
            return $this;
        }
        foreach ($options as $_k => $_v) {
            $method = 'set' . ucwords($_k);
            if (method_exists($this, $method)) {
                $this->$method($_v);
            } else {
                throw new Exception('No such method exists: ' . $method . 'in ' . __CLASS__);
            }
        }
        return $this;
    }

    /**
     * Render the template with tokens.
     *
     * @return string The rendered string.
     */
    public function render()
    {
        return $this->_replaceTokens();
    }

    /**
     * Set prefix for the token holder.
     *
     * @param string $prefix
     * @return Ext_SimpleTemplate
     */
    public function setTokenHolderPrefix($prefix)
    {
        $this->_tokenHolderPrefix = $prefix;
        return $this;
    }

    /**
     * Set postfix for the token holder.
     *
     * @param string $postfix
     * @return Ext_SimpleTemplate
     */
    public function setTokenHolderPostfix($postfix)
    {
        $this->_tokenHolderPostfix = $postfix;
        return $this;
    }

    /**
     * Set tokens.
     *
     * @param array $tokens
     * @return boolean
     */
    public function setTokens($tokens)
    {
        if (!is_array($tokens)) {
            return false;
        }
        $this->_tokens = $tokens;
        return true;
    }

    /**
     * Set template string.
     *
     * @param string $templateString
     * @return Ext_SimpleTemplate
     */
    public function setTemplateString($templateString)
    {
        $this->_templateString = $templateString;
        return $this;
    }

    /**
     * Remove token holder if empty.
     *
     * @param boolean $remove
     * @return Ext_SimpleTemplate
     */
    public function setRemoveTokenHolderIfEmpty($remove)
    {
        $this->_removeTokenHolderIfEmpty = (bool)$remove;
        return $this;
    }

    /**
     * Add a token to the token array
     *
     * @param string $token
     * @param string $value
     * @return boolean
     */
    public function addToken($token, $value) {
        $this->_tokens[$token] = $value;
        return true;
    }

    /**
     * Remove a token from the token array
     *
     * @param string $token
     * @return boolean
     */
    public function removeToken($token) {
        if (isset($this->_tokens[$token])) {
            unset($this->_tokens[$token]);
            return true;
        }
        return false;
    }

    /**
     * Get a list of template tokens.
     *
     * @param string $template
     * @return array
     */
    public function getTemplateTokens($template)
    {
        $regex = '/'
            . $this->_regexEscapge($this->_tokenHolderPrefix)
            . '(.+)'
            . $this->_regexEscapge($this->_tokenHolderPostfix)
            . '/iU';
        preg_match_all($regex, $template, $tokens);
        return $tokens[1];
    }

    /**
     * Replace the tokens.
     *
     * @return string
     */
    protected function _replaceTokens()
    {
        $regex = '/'
            . $this->_regexEscapge($this->_tokenHolderPrefix)
            . '(.+)'
            . $this->_regexEscapge($this->_tokenHolderPostfix)
            . '/iU';
        $renderedString = preg_replace_callback(
            $regex,
            array(&$this, '_replaceTokenByValue'),
            $this->_templateString
        );
        return $renderedString;
    }

    /**
     * Callback function for replacing a token.
     *
     * @param array $matches
     * @return string
     */
    protected function _replaceTokenByValue($matches)
    {
        $tokenKey = $matches[1];
        if (isset($this->_tokens[$tokenKey])) {
            return $this->_tokens[$tokenKey];
        } else {
            if ($this->_removeTokenHolderIfEmpty) {
                return '';
            } else {
                // If not found, return the original matched string.
                return $matches[0];
            }
        }
    }

    /**
     * In case we need to escape the string in a regex.
     *
     * @param string $string
     * @return string
     */
    protected function _regexEscapge($string)
    {
        $originals = str_split($string);
        $replaced = '';
        foreach ($originals as $_a) {
            $replaced .= '\\' . $_a;
        }
        return $replaced;
    }
}