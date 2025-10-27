<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use App\Application\Post\GetAllPostsService;
use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\Entity\Post;

final class GetAllPostsServiceTest extends TestCase
{
    public function testExecuteReturnsPostsArray(): void
    {
        $post = new Post(1, 'T', 'C', 2, (new DateTimeImmutable())->format(DATE_ATOM));

        $repo = $this->createMock(PostRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getAllPosts')
            ->willReturn([$post]);

        $service = new GetAllPostsService($repo);

        $result = $service->execute();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Post::class, $result[0]);
        $this->assertSame(1, $result[0]->getId());
    }
}
