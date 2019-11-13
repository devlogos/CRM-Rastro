<?php

/**
 * dashboard controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

class DashboardController
{
    public function dashboardView() {
        \App\View::make('Dashboard/Dashboard');
    }
}