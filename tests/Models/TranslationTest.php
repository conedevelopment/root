<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Translation;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class TranslationTest extends TestCase
{
    protected User $user;

    protected Translation $translation;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->translation = Translation::factory()->for($this->user, 'translatable')->create([
            'values' => ['name' => 'Translated Name'],
        ]);
    }

    public function test_a_translation_belongs_to_translatable(): void
    {
        $this->assertTrue(
            $this->translation->translatable->is($this->user)
        );
    }

    public function test_a_translatable_model_has_many_translation(): void
    {
        $this->assertTrue(
            $this->user->translations->contains($this->translation)
        );
    }

    public function test_a_translatable_model_attributes_can_be_translated(): void
    {
        Translation::setTranslatableLocale('hu');

        $this->assertSame('hu', Translation::getTranslatableLocale());

        $this->assertSame(
            $this->translation->values['name'],
            $this->user->translate('name', $this->translation->locale)
        );

        $this->assertSame(
            $this->user->name,
            $this->user->translate('name', Translation::getTranslatableLocale())
        );

        $this->assertNull(
            $this->user->translate('name', 'fake')
        );
    }
}
