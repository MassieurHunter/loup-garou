<!DOCTYPE html>
<html lang="{$html_lang}">
<head>
    <meta charset="utf-8">
    <meta name="description"
          content="Loup Garou">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Loup Garou</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/noty.css">
    <link rel="stylesheet" href="/css/bootstrap-v4.css">
    <link rel="stylesheet" data-type="theme" href="">
    <link rel="stylesheet" href="/css/style.css?v={$timestamp}">
    <base href="{$baseUrl}">
</head>
<body>
<div class="container">
    <h1 class="text-center">{$lang['title']}</h1>
    <div class="themes-selector-container">
        <div class="input-group">
            <div class="input-group-prepend">
                <label class="input-group-text" for="lg-theme-selector">
                    {$lang['change_theme']}
                </label>
            </div>
            <select class="custom-select themes-selector" id="lg-theme-selector">
                <option value="">{$lang['theme_default']}</option>
                {foreach $themes as $folder => $theme}
                    <option value="{$folder}">{$theme}</option>
                {/foreach}
            </select>
        </div>
    </div>