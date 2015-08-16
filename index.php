<?php

session_cache_limiter(false);
session_start();

// Activate autloading
require 'vendor/autoload.php';

// Initiate app
$app = new \Slim\Slim(array(
    'mode' => getenv('MODE'),
    'view' => new \Slim\Views\Twig(),
));

// Settings for production
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'debug' => false,
        'log.enabled' => false,
    ));
});

// Settings for development
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'debug' => true,
        'log.enabled' => true,
        'log.writer' => new \Slim\Logger\DateTimeFileWriter(),
    ));
});

// Custom view settings
$app->view()->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__) . '/cache',
    'strict_variables' => false,
);
$app->view()->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

// Routes
$app->get('/', function () use ($app) {
    $app->render('pages/home.html.twig', array(
        'body_id' => 'home',
        'html_title' => 'Bijles wiskunde A, B, C en D',
        'html_description' => 'Kan je wel wat bijles wiskunde gebruiken? Neem dan contact op met De Bijlesformule.',
        'header_class' => 'hero',
        'js_min' => file_get_contents('includes/script.min.js'),
        'css_min' => file_get_contents('includes/home.css'),
    ));
});

$app->get('/eindhoven', function () use ($app) {
    $app->render('pages/eindhoven.html.twig', array(
        'body_id' => 'eindhoven',
        'html_title' => 'Bijles wiskunde A, B, C en D in Eindhoven',
        'html_description' => 'De Bijlesformule geeft wiskundebijles in Eindhoven. Kan je wel wat bijles wiskunde gebruiken? Neem dan contact op met De Bijlesformule.',
        'header_class' => 'normal',
        'js_min' => file_get_contents('includes/script.min.js'),
        'css_min' => file_get_contents('includes/home.css'),
    ));
});

$app->get('/over-mij', function () use ($app) {
    $app->render('pages/over-mij.html.twig', array(
        'body_id' => 'about-us',
        'html_title' => 'Over mij',
        'header_class' => 'normal',
        'js_min' => file_get_contents('includes/script.min.js'),
        'css_min' => file_get_contents('includes/home.css'),
    ));
});

$app->get('/pakketten', function () use ($app) {

    $app->render('pages/pakketten.html.twig', array(
        'body_id' => 'pricing',
        'html_title' => 'Pakketten',
        'header_class' => 'normal',
        'js_min' => file_get_contents('includes/script.min.js'),
        'css_min' => file_get_contents('includes/home.css'),
        'default_rate' => 25,
        'packages' => array(
            array(
                'name' => 'Pakket A',
                'class' => 'first',
                'price' => 135,
                'hours' => 6,
            ),
            array(
                'name' => 'Pakket B',
                'class' => 'featured',
                'price' => 240,
                'hours' => 12,
            ),
            array(
                'name' => 'Pakket C',
                'class' => 'last',
                'price' => 420,
                'hours' => 24,
            ),
        ),
    ));
});

$app->get('/contact', function () use ($app) {

    $question = $app->request->get('vraag');

    switch($question){
        case 'kennismakingsgesprek':
            $message = 'Zou je contact met mij kunnen opnemen om een kennismakingsgesprek in te plannen?';
            break;
        case 'Pakket A':
        case 'Pakket B':
        case 'Pakket C':
            $message = 'Graag zou ik ' . $question . ' willen gebruiken.'
                . ' Wanneer is het mogelijk om de eerste bijles te houden?';
            break;
        case 'beschikbaarheid':
            $message = 'Op welke dagen zou jij bijles kunnen geven?';
            break;
        default:
            $message = '';
    }

    $app->render('pages/contact.html.twig', array(
        'body_id' => 'contact-us',
        'html_title'=> 'Contact',
        'header_class' => 'normal',
        'js_min' => file_get_contents('includes/script.min.js'),
        'css_min' => file_get_contents('includes/home.css'),
        'message' => $message,
    ));
});

$app->post('/contact', function () use ($app) {

    $keys = array('name', 'email', 'phone', 'level', 'year', 'subject', 'message');
    $params = array();

    foreach ($keys as $key) {
        $params[$key] = $app->request->post($key);
    }

    $html = $app->view->render('email.html.twig', $params);

    $mandrill = new Mandrill();

    $message = array(
        'html' => $html,
        'subject' => 'Contactformulier | ' . $params['name_student'],
        'from_email' => 'info@debijlesformule.nl',
        'from_name' => 'De Bijlesformule',
        'to' => array(
            array(
                'email' => 'mail@markbiesheuvel.nl',
                'name' => 'Mark Biesheuvel',
                'type' => 'to'
            )
        ),
        'headers' => array('Reply-To' => $params['email']),
        'important' => false,
        'tags' => array('contact-form'),
        'track_opens' => false,
        'track_clicks' => false,
        'auto_text' => true,
        'inline_css' => true,
    );

    $result = $mandrill->messages->send($message, false);

    if($result[0]['status'] == 'sent'){
        $app->flash('info', 'Uw bericht is verzonden. Ik zal hier zo spoedig mogelijk op reageren.');
    }else{
        $app->flash('error', 'Er is iets mis gegaan tijdens het versuren van uw bericht. U kunt uw bericht ook mailen naar <a href="mailto:mail@markbiesheuvel.nl">mail@markbiesheuvel.nl</a>.');
    }
    $app->redirect('/contact');
});

$app->get('/ping', function () use ($app) {
    $app->response->headers->set('Content-Type', 'text/plain');
    echo 'pong';
});

$app->run();