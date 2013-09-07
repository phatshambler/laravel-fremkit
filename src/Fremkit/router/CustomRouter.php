<?php namespace Fremkit\Router;

class CustomRouter extends \Illuminate\Routing\Router {

    protected $resourceDefaults = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'undelete', 'up', 'down', 'kill', 'activate', 'deactivate');

    /**
     * Add the show method for a resourceful route.
     *
     * @param  string  $name
     * @param  string  $base
     * @param  string  $controller
     * @return void
     */
    protected function addResourceUndelete($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/undelete';

        return $this->get($uri, $this->getResourceAction($name, $controller, 'undelete'));
    }

    protected function addResourceUp($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/up';

        return $this->get($uri, $this->getResourceAction($name, $controller, 'up'));
    }

    protected function addResourceDown($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/down';

        return $this->get($uri, $this->getResourceAction($name, $controller, 'down'));
    }

    protected function addResourceActivate($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/activate';

        return $this->get($uri, $this->getResourceAction($name, $controller, 'activate'));
    }

    protected function addResourceDeactivate($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/deactivate';

        return $this->get($uri, $this->getResourceAction($name, $controller, 'deactivate'));
    }

    protected function addResourceKill($name, $base, $controller)
    {
        $uri = $this->getResourceUri($name).'/{'.$base.'}/kill';

        return $this->get($uri, $this->getResourceAction($name, $controller, 'kill'));
    }

}