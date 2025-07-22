<?php

namespace Tests\Unit\Services\Payment;

use Tests\TestCase;
use App\Services\Payment\CardValidationService;

class CardValidationServiceTest extends TestCase
{
    protected $cardValidationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cardValidationService = new CardValidationService();
    }

    /** @test */
    public function it_validates_valid_card_numbers()
    {
        $validCards = [
            '4242424242424242', // Visa test card
            '5555555555554444', // Mastercard test card
            '378282246310005',  // Amex test card
        ];

        foreach ($validCards as $cardNumber) {
            $this->assertTrue(
                $this->cardValidationService->validateCardNumber($cardNumber),
                "Card number {$cardNumber} should be valid"
            );
        }
    }

    /** @test */
    public function it_rejects_invalid_card_numbers()
    {
        $invalidCards = [
            '1234567890123456', // Invalid Luhn
            '424242424242424',  // Too short
            '42424242424242424242', // Too long
            '424242424242424a', // Contains letter
            '',                 // Empty
            '123',              // Way too short
        ];

        foreach ($invalidCards as $cardNumber) {
            $this->assertFalse(
                $this->cardValidationService->validateCardNumber($cardNumber),
                "Card number {$cardNumber} should be invalid"
            );
        }
    }

    /** @test */
    public function it_validates_cards_with_spaces_and_dashes()
    {
        $cardWithSpaces = '4242 4242 4242 4242';
        $cardWithDashes = '4242-4242-4242-4242';
        $cardMixed = '4242 4242-4242 4242';

        $this->assertTrue($this->cardValidationService->validateCardNumber($cardWithSpaces));
        $this->assertTrue($this->cardValidationService->validateCardNumber($cardWithDashes));
        $this->assertTrue($this->cardValidationService->validateCardNumber($cardMixed));
    }

    /** @test */
    public function it_can_detect_card_brands()
    {
        $testCards = [
            '4242424242424242' => 'visa',
            '5555555555554444' => 'mastercard',
            '378282246310005' => 'amex',
            '6011111111111117' => 'discover',
            '30569309025904' => 'diners',
            '3530111333300000' => 'jcb',
        ];

        foreach ($testCards as $cardNumber => $expectedBrand) {
            $detectedBrand = $this->cardValidationService->detectCardBrand($cardNumber);
            $this->assertEquals(
                $expectedBrand,
                $detectedBrand,
                "Card {$cardNumber} should be detected as {$expectedBrand}"
            );
        }
    }

    /** @test */
    public function it_returns_null_for_unknown_card_brands()
    {
        $unknownCard = '1234567890123456';
        $brand = $this->cardValidationService->detectCardBrand($unknownCard);
        
        $this->assertNull($brand);
    }

    /** @test */
    public function it_can_format_card_numbers()
    {
        $cardNumber = '4242424242424242';
        $formatted = $this->cardValidationService->formatCardNumber($cardNumber);
        
        $this->assertEquals('4242 4242 4242 4242 ', $formatted);
    }

    /** @test */
    public function it_can_mask_card_numbers()
    {
        $cardNumber = '4242424242424242';
        $masked = $this->cardValidationService->maskCardNumber($cardNumber);
        
        $this->assertEquals('**** **** **** 4242 ', $masked);
    }

    /** @test */
    public function it_masks_short_cards_completely()
    {
        $shortCard = '123';
        $masked = $this->cardValidationService->maskCardNumber($shortCard);
        
        $this->assertEquals('***', $masked);
    }

    /** @test */
    public function it_provides_test_cards()
    {
        $testCards = $this->cardValidationService->getTestCards();
        
        $this->assertIsArray($testCards);
        $this->assertArrayHasKey('visa', $testCards);
        $this->assertArrayHasKey('mastercard', $testCards);
        $this->assertArrayHasKey('amex', $testCards);
        
        // Verify test cards are valid
        foreach ($testCards as $brand => $cardNumber) {
            $this->assertTrue(
                $this->cardValidationService->validateCardNumber($cardNumber),
                "Test card for {$brand} should be valid"
            );
        }
    }

    /** @test */
    public function it_can_handle_empty_input_gracefully()
    {
        $this->assertFalse($this->cardValidationService->validateCardNumber(''));
        $this->assertNull($this->cardValidationService->detectCardBrand(''));
        $this->assertEquals('', $this->cardValidationService->formatCardNumber(''));
        $this->assertEquals('', $this->cardValidationService->maskCardNumber(''));
    }

    /** @test */
    public function luhn_algorithm_works_correctly()
    {
        // Test known valid Luhn numbers
        $validLuhnNumbers = [
            '4242424242424242',
            '79927398713',
            '1234567890123452',
        ];

        foreach ($validLuhnNumbers as $number) {
            $this->assertTrue(
                $this->cardValidationService->validateCardNumber($number),
                "Luhn valid number {$number} should pass validation"
            );
        }

        // Test known invalid Luhn numbers  
        $invalidLuhnNumbers = [
            '4242424242424241', // Last digit wrong
            '79927398712',      // Last digit wrong
            '1234567890123451', // Last digit wrong
        ];

        foreach ($invalidLuhnNumbers as $number) {
            $this->assertFalse(
                $this->cardValidationService->validateCardNumber($number),
                "Luhn invalid number {$number} should fail validation"
            );
        }
    }

    /** @test */
    public function it_validates_card_length_constraints()
    {
        // Test minimum length (13)
        $minValidCard = '4000000000002'; // 13 digits, valid Luhn
        $this->assertTrue($this->cardValidationService->validateCardNumber($minValidCard));

        // Test maximum length (19)
        $maxValidCard = '4000000000000000002'; // 19 digits, valid Luhn 
        $this->assertTrue($this->cardValidationService->validateCardNumber($maxValidCard));

        // Test too short (12)
        $tooShort = '400000000000';
        $this->assertFalse($this->cardValidationService->validateCardNumber($tooShort));

        // Test too long (20)
        $tooLong = '40000000000000000002';
        $this->assertFalse($this->cardValidationService->validateCardNumber($tooLong));
    }

    /** @test */
    public function it_handles_special_characters_in_formatting()
    {
        $cardWithSpecialChars = '4242-4242 4242_4242';
        
        // Should still validate after cleaning
        $this->assertTrue($this->cardValidationService->validateCardNumber($cardWithSpecialChars));
        
        // Should format properly
        $formatted = $this->cardValidationService->formatCardNumber($cardWithSpecialChars);
        $this->assertEquals('4242 4242 4242 4242 ', $formatted);
    }
} 