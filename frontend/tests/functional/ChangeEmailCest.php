<?php

namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;

class ChangeEmailCest {
	protected $formId = '#change-email-form';


	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 * @see \Codeception\Module\Yii2::_before()
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @return array
	 */
	public function _fixtures()
	{
		return [
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		];
	}

	public function _before(FunctionalTester $I) {
		$I->amOnRoute('/site/login');
		$I->submitForm('#login-form', [
			'LoginForm[username]' => 'test2.test',
			'LoginForm[password]' => 'Test1234',
		]);
		$I->amOnRoute('/site/change-email');
	}

	protected function formParams($email)
	{
		return [
			'ChangeEmailForm[email]' => $email
		];
	}

	public function checkPage(FunctionalTester $I)
	{
		$I->see('Change email', 'h1');
		$I->see('Please fill out your email. A verification email will be sent there.');
	}

	public function checkEmptyField(FunctionalTester $I)
	{
		$I->submitForm($this->formId, $this->formParams(''));
		$I->seeValidationError('Email cannot be blank.');
	}

	public function checkWrongEmailFormat(FunctionalTester $I)
	{
		$I->submitForm($this->formId, $this->formParams('abcd.com'));
		$I->seeValidationError('Email is not a valid email address.');
	}


	public function checkAlreadyVerifiedEmail(FunctionalTester $I)
	{
		$I->submitForm($this->formId, $this->formParams('test2@mail.com'));
		$I->seeValidationError('This email address has already been taken.');
		$I->submitForm($this->formId, $this->formParams('test@mail.com'));
		$I->seeValidationError('This email address has already been taken.');
	}

	public function checkSendSuccessfully(FunctionalTester $I)
	{
		$I->submitForm($this->formId, $this->formParams('new@mail.com'));
		$I->canSeeEmailIsSent();
		$I->see('Check your email for further instructions.');
	}
}
