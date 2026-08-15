<?php

declare(strict_types=1);

use OpenCode\MultichannelLogger\Slack\SlackAttachmentData;
use OpenCode\MultichannelLogger\Slack\SlackMessageData;

it('builds a slack payload from a message', function () {
    $message = SlackMessageData::from([
        'text' => 'Hello world',
        'channel' => '#general',
        'username' => 'Bot',
        'iconEmoji' => ':wave:',
    ]);

    expect($message->toPayload())->toMatchArray([
        'text' => 'Hello world',
        'channel' => '#general',
        'username' => 'Bot',
        'icon_emoji' => ':wave:',
    ]);
});

it('includes attachments in the payload', function () {
    $message = new SlackMessageData(
        text: 'Deploy finished',
        attachments: collect([new SlackAttachmentData(
            color: 'good',
            title: 'Release v1.0.0',
            text: 'Everything went well.',
        )]),
    );

    $payload = $message->toPayload();

    expect($payload['attachments'][0])->toMatchArray([
        'color' => 'good',
        'title' => 'Release v1.0.0',
    ]);
});

it('keeps a false markdown flag in the payload', function () {
    $message = new SlackMessageData(text: 'plain', markdown: false);

    expect($message->toPayload()['mrkdwn'])->toBeFalse();
});
