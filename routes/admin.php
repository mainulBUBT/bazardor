<?php

use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "admin", "as"=> "admin."], function () {
    Route::get("/", function () {
        return redirect()->route('admin.dashboard');
    });
    
    Route::get("/dashboard", function () {
        return view("admin.dashboard");
    })->name('dashboard');
});
