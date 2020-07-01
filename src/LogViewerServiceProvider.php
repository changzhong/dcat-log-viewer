<?php

namespace Dcat\Admin\Extension\LogViewer;

use Dcat\Admin\Admin;
use Illuminate\Support\ServiceProvider;

class LogViewerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $extension = LogViewer::make();

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, LogViewer::NAME);
        }

        $this->app->booted(function () use ($extension) {
            $extension->routes(__DIR__.'/../routes/web.php');
        });

        // 添加菜单
        $this->registerMenus();
    }

    protected function registerMenus()
    {
        Admin::menu()->add([
            [
                'id'            => 1,
                'title'         => '日志',
                'icon'          => ' fa-newspaper-o',
                'uri'           => 'logs',
                'parent_id'     => 0,
                'permission_id' => 'log-viewer', // 绑定权限
                'roles'         => [['slug' => 'log-viewer']], // 绑定角色
            ]
        ]);
    }
}
