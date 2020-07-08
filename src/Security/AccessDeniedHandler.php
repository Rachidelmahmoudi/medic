<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    /*
    * @var Twig\Environment;
    */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $content = 'Pas de droit à voir cette resource';
        //$this->twig->render('403.html.twig',['code'=> 403,'message'=>'Pas de droit à voir cette resource','title'=>'Acces refusé']);
        return new Response($content, 403);
    }
}
