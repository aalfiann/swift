<?php
namespace modules\session\twig;
use \modules\session\helper\SessionHelper;

class SessionTwigExtension extends \Twig_Extension
{
    public function __construct()
    {
        $this->session = new SessionHelper();
    }

    public function getName()
    {
        return 'Session Twig Extension';
    }
    
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('session_get', array($this, 'session_get')),
            new \Twig_SimpleFunction('session_set', array($this, 'session_set')),
            new \Twig_SimpleFunction('session_merge', array($this, 'session_merge')),
            new \Twig_SimpleFunction('session_exists', array($this, 'session_exists')),
            new \Twig_SimpleFunction('session_delete', array($this, 'session_delete')),
            new \Twig_SimpleFunction('session_clear', array($this, 'session_clear'))
        ];
    }

    public function session_get($key)
    {
        return $this->session->get($key);
    }

    public function session_set($key,$value)
    {
        return $this->session->set($key,$value);
    }

    public function session_merge($key,$value)
    {
        return $this->session->merge($key,$value);
    }

    public function session_exists($key)
    {
        return $this->session->exists($key);
    }

    public function session_delete($key)
    {
        return $this->session->delete($key);
    }

    public function session_clear()
    {
        return $this->session->clear();
    }
}
