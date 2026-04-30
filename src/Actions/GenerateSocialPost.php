<?php

namespace Infofactory\StatamicAiSocial\Actions;

use Statamic\Actions\Action;

class GenerateSocialPost extends Action
{
    protected static $title = 'statamic-ai-social::cp.generate.title';
    protected $confirm = false;
    public function redirect($items, $values)
    {
        return cp_route('statamic-ai-social.generate-page', $items->first()->id());
    }

    public function visibleTo($item)
    {
        $isEntry = $item instanceof \Statamic\Entries\Entry;
        return $isEntry;
    }
}
