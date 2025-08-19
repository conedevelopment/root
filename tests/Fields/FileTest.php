<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\File;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Http\UploadedFile;

class FileTest extends TestCase
{
    protected File $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new File('Documents');
    }

    public function test_a_file_field_has_file_template(): void
    {
        $this->assertSame('root::fields.file', $this->field->getTemplate());
    }

    public function test_a_file_field_stores_files(): void
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('cert.png');

        $this->app['request']->setUserResolver(fn () => $user);
        $this->app['request']->files->add(['documents' => $file]);

        $this->field->collection('documents');
        $this->field->persist($this->app['request'], $user, []);

        $user->save();

        $this->assertTrue($user->refresh()->documents->where('file_name', 'cert.png')->isNotEmpty());
    }
}
