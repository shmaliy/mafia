<?php

class view
{
    public function assign($bind)
    {
        foreach ($bind as $key => $name) {
            $this->$key = $name;
            unset($key);
            unset($name);
        }
        unset($bind);
    }
    
    public function render()
    {
        ob_start();
        if (false !== @func_get_arg(1)) {
            $this->assign(func_get_arg(1));
        }
        include func_get_arg(0);
        return ob_get_clean();        
    }
}