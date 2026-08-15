# Changelog

All notable changes to `mahmoud-hamed/multichannel-logger` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Slack integration: log channel, notification channel, formatter, messenger and message data
- Discord integration: log channel, notification channel, formatter, messenger and message data
- Zoom integration: log channel, notification channel, formatter, messenger and message data
- `MultichannelLogger` service and facade for sending messages with the configured default webhooks
- Customizable client defaults (timeout, retry times, retry interval)
- Retry support for `429` and `5xx` responses via Saloon
- Domain exceptions: `MissingWebhookException`, `InvalidWebhookException`, `FailedRequestException`, `MissingConfigurationException`
- Pest test suite (unit + feature) and architecture tests
- PHPStan (level 7 + strict rules) and Laravel Pint configuration
