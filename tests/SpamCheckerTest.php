<?php

namespace App\Tests;

use App\Entity\Comment;
use App\Enum\SpamStatus;
use App\SpamChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpamCheckerTest extends TestCase
{
    /**
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testSpamScoreWithInvalidRequest(): void
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $client = new MockHttpClient([new MockResponse('invalid', ['response_headers' => ['x-akismet-debug-help: Invalid key']])]);
        $checker = new SpamChecker($client, 'cf08d57d5438');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');
        $checker->getSpamScore($comment, $context);
    }

    /**
     * @param  SpamStatus  $expectedScore
     * @param  ResponseInterface  $response
     * @param  Comment  $comment
     * @param  array  $context
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testSpamScore(SpamStatus $expectedScore, ResponseInterface $response, Comment $comment, array $context)
    {
        $client = new MockHttpClient([$response]);
        $checker = new SpamChecker($client, 'cf08d57d5438');

        $score = $checker->getSpamScore($comment, $context);
        $this->assertSame($expectedScore, $score);
    }

    /**
     * @return iterable
     */
    public static function provideComments(): iterable
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $response = new MockResponse('', ['response_headers' => ['x-akismet-pro-tip: discard']]);
        yield 'blatant_spam' => [SpamStatus::SPAM, $response, $comment, $context];

        $response = new MockResponse('true');
        yield 'spam' => [SpamStatus::POSSIBLE_SPAM, $response, $comment, $context];

        $response = new MockResponse('false');
        yield 'ham' => [SpamStatus::NOT_SPAM, $response, $comment, $context];
    }
}
