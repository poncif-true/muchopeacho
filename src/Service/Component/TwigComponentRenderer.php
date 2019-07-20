<?php


namespace App\Service\Component;


/**
 * Class TwigComponentRenderer
 *
 * @package App\Service
 */
class TwigComponentRenderer
{
    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;

    /**
     * TwigComponentRenderer constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Returns a raw html rendered view
     * @param Renderable $component
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(Renderable $component)
    {
        return $this->twig->render($component->getTemplate(), $component->getContext());
    }
}
