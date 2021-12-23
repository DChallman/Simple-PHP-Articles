# Simple PHP Articles

A simple way to implement an article system without the need of a framework or database or CRUD interface. Simply create a new .spa file inside the Articles directory located in the root of your web site. See `example.spa` for a sample article format.

## Install and setup

`composer require dchallman/simple-php-articles`

Include the initialization of SPA at the top of each file you want to use it in

```php
<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
  $SPA = dchallman\SimplePHPArticles\SPA::getInstance();
  $SPA->init();
?>
```

Include the main article view to `articles.php`

```php
  <div style="font-size:20px;">
      <?php $SPA->show();?>
  </div>
```

Include the latest article preview to any page you want to support the latest view

```php
  <?php $SPA->latest(); ?>
```

## Creating a new article

Create an `Articles` directory inside the root of your web project

Inside the `Articles` directory create a new file with the `.spa` file type

Look at `example.spa` for an article example, it goes:

- Date

- Title

- Body (HTML optional)