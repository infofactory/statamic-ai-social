<?php

namespace Infofactory\StatamicAiSocial\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Site;
use Statamic\Http\Controllers\Controller;

class GeneratePostController extends Controller
{
    public function index(Entry $entry)
    {
        $previewEntry = $entry->fromWorkingCopy();
        EntryFacade::substitute($previewEntry);
        $previewEntry->setSupplement('live_preview', true);

        $currentRequest = request();
        $entryUrl = $previewEntry->url();
        $entryRequest = Request::create(
            $entryUrl,
            'GET',
            [],
            $currentRequest->cookies->all(),
            [],
            array_merge($currentRequest->server->all(), [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => $entryUrl,
                'PATH_INFO' => $entryUrl,
                'QUERY_STRING' => '',
            ])
        );

        app()->instance('request', $entryRequest);
        Site::setCurrent($previewEntry->locale());
        $entry_response = $previewEntry->toResponse($entryRequest);
        app()->instance('request', $currentRequest);

        $entry_html = $entry_response->getContent();

        $socials = collect(config('statamic-ai-social.prompts'))->map(function ($prompt) {
            return [
                'id' => $prompt['id'],
                'name' => $prompt['name'],
            ];
        });

        return view('statamic-ai-social::generate', [
            'entry' => $entry,
            'entry_html' => $entry_html,
            'socials' => $socials,
        ]);
    }

    public function generate(Request $request, Entry $entry)
    {
        $social_id = $request->input('social');
        $page_content = $request->input('page_content');
        $prompts = collect(config('statamic-ai-social.prompts'));
        $prompt = $prompts->where('id', $social_id)->first();

        $systemPrompt = "You are a social media post generator that generates posts for {$prompt['name']}.";

        if ($prompt['instructions']) {
            $systemPrompt .= "\n\n#Style instructions  \n".$prompt['instructions'];
        }

        $systemPrompt .= "\n\n Only reply with the post text. Do not include any HTML or other formatting. \n Generate the post for the following entry.";

        return response()->stream(function () use ($prompt, $page_content, $systemPrompt) {
            function send($data)
            {
                echo 'data: '.json_encode($data);
                echo "\n\n";
                ob_flush();
                flush();
            }

            $stream = Prism::text()
                ->using($prompt['provider'], $prompt['model'])
                ->withSystemPrompt($systemPrompt)
                ->withMessages([new UserMessage($page_content)])
                ->asStream();

            foreach ($stream as $event) {
                if ($event instanceof TextDeltaEvent) {
                    send([
                        'type' => 'chunk',
                        'delta' => $event->delta,
                    ]);
                }
            }

            send([
                'type' => 'end',
            ]);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no', // Prevents Nginx from buffering
        ]);

    }
}
