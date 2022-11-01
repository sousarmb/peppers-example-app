<?php

namespace App\Controllers\Example;

use App\Events\Example\BlockingEvent;
use App\Events\Example\DeferredEvent;
use Peppers\Helpers\ViewDataStore;
use Peppers\Renderer\HtmlView;
use Peppers\Response;

class Events {

    /**
     * 
     * @return Response
     */
    public function blocking(BlockingEvent $blocker): Response {
        /*
         * 
         * bla, bla, bla lots of useful code here
         * 
         */
        // events are deferred by default, this call makes them block
        $blocker->setIsDeferred(false)
                ->setData([
                    'Where is everyone? I guess i\'m first again!'
                ])
                ->dispatch();
        /*
         * 
         * bla, bla, bla lots of useful code here
         * 
         */
        $store = new ViewDataStore([
            'exampleName' => 'Blocking Event'
        ]);
        $view = new HtmlView('examples.events.blocking-or-deferred.content', $store);
        return (new Response)->html($view);
    }

    /**
     * 
     * @param DeferredEvent $deferred
     * @return Response
     */
    public function deferred(DeferredEvent $deferred): Response {
        /*
         * 
         * bla, bla, bla lots of useful code here
         * 
         */
        $deferred->setData([
                    'Am i late to the party again?!'
                ])
                ->dispatch();
        /*
         * 
         * bla, bla, bla lots of useful code here
         * 
         */
        $store = new ViewDataStore([
            'exampleName' => 'Blocking Event'
        ]);
        $view = new HtmlView('examples.events.blocking-or-deferred.content', $store);
        return(new Response)->html($view);
    }

}
