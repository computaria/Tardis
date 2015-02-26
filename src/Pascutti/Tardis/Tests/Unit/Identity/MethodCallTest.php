<?php

namespace Pascutti\Tardis\Identity;

/**
 * @small
 */
class MethodCallTest extends \PHPUnit_Framework_TestCase
{
    private $generator = null;

    public function setUp()
    {
        $this->generator = new MethodCall;
    }

    /**
     * @test
     * @dataProvider provideInvalidNumberOfArguments
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected 2 arguments to create an ID
     */
    public function call_without_right_number_of_arguments_fail(array $wrongNumberOfArguments)
    {
        $callable = [$this->generator, 'createIdFor'];

        call_user_func_array($callable, $wrongNumberOfArguments);
    }

    public static function provideInvalidNumberOfArguments()
    {
        return [
            [[]], // No arguments
            [[1]], // One argument
            [[1, 2, 3]], // Three arguments
        ];
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage An argument list should be always an array.
     */
    public function call_with_method_arguments_no_being_an_array_fail()
    {
        $this->generator->createIdFor('myMethod', 'not-an-agument-list');
    }

    /**
     * @test
     */
    public function identity_for_same_method_and_arguments_should_be_the_same()
    {
        $methodName = 'travelTo';
        $methodArguments = ['2015AD', '1018AD'];

        $firstIdentity = $this->generator->createIdFor($methodName, $methodArguments);
        $secondIdentity = $this->generator->createIdFor($methodName, $methodArguments);

        $this->assertEquals(
            $firstIdentity,
            $secondIdentity,
            'Given the same arguments, identity should be the same.'
        );
    }

    /**
     * @test
     */
    public function identity_for_different_arguments_should_be_different()
    {
        $firstIdentity = $this->generator->createIdFor('createFromFormat', ['d', '15']);
        $secondIdentity = $this->generator->createIdFor('createFromFormat', ['m', '12']);

        $this->assertNotEquals(
            $firstIdentity,
            $secondIdentity,
            'Different method calls (with different arguments) should result in different identities.'
        );
    }

    /**
     * @test
     */
    public function identity_for_method_arguments_that_cannot_be_serialized()
    {
        $this->markTestSkipped('A unserializable argument is not currently supported.');

        $unserializableArgument = new \Pdo('sqlite::memory:');
        $methodName = 'createIdForUnserializableArgument';
        $methodArguments = [$unserializableArgument];

        $firstIdentity = $this->generator->createIdFor($methodName, $methodArguments);
        $secondIdentity = $this->generator->createIdFor($methodName, $methodArguments);

        $this->assertEquals(
            $firstIdentity,
            $secondIdentity,
            'Given the same arguments, identity should be the same.'
        );
    }
}
