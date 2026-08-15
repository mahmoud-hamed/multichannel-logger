<?php

declare(strict_types=1);

use MahmoudHamed\MultichannelLogger\Discord\DiscordEmbedData;
use MahmoudHamed\MultichannelLogger\Discord\DiscordMessageData;

it('builds a discord payload from a message', function () {
    $message = new DiscordMessageData(
        content: 'Hello world',
        username: 'Bot',
        avatarUrl: 'https://example.com/avatar.png',
    );

    expect($message->toPayload())->toMatchArray([
        'content' => 'Hello world',
        'username' => 'Bot',
        'avatar_url' => 'https://example.com/avatar.png',
    ]);
});

it('includes embeds in the payload', function () {
    $message = new DiscordMessageData(
        content: 'Warning',
        embeds: collect([new DiscordEmbedData(
            title: 'Disk space low',
            description: '5% remaining',
            color: 0xE01E5A,
        )]),
    );

    $payload = $message->toPayload();

    expect($payload['embeds'][0])->toMatchArray([
        'title' => 'Disk space low',
        'description' => '5% remaining',
        'color' => 0xE01E5A,
    ]);
});
