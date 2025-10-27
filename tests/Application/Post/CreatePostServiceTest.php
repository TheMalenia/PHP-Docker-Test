<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Application\Post\CreatePostService;
use App\Domain\Repository\PostRepositoryInterface;

final class CreatePostServiceTest extends TestCase
{
    public function testExecuteCreatesPostAndReturnsTrue(): void
    {
        $repo = $this->createMock(PostRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('createPost')
            ->with('My Title', 'Some content', 2)
            ->willReturn(true);

        $service = new CreatePostService($repo);

        $result = $service->execute('  My Title  ', ' Some content ', 2);

        $this->assertTrue($result);
    }

    public function testExecuteInvalidInputThrowsException(): void
    {
        $repo = $this->createMock(PostRepositoryInterface::class);
        $service = new CreatePostService($repo);

        $this->expectException(\InvalidArgumentException::class);
        $service->execute('', 'content', 1);
    }
}
