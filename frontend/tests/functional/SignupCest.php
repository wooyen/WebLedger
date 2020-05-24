<?php

namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use common\models\User;
use frontend\tests\FunctionalTester;

class SignupCest
{
	protected $formId = '#form-signup';

	public function _fixtures() {
		return [
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		];
	}

	public function _before(FunctionalTester $I)
	{
		$I->amOnRoute('site/signup');
	}

	public function signupWithEmptyFields(FunctionalTester $I)
	{
		$I->see('Signup', 'h1');
		$I->see('Please fill out the following fields to signup:');
		$I->submitForm($this->formId, []);
		$I->seeValidationError('Username cannot be blank.');
		$I->seeValidationError('Email cannot be blank.');
		$I->seeValidationError('Password cannot be blank.');
		$I->seeValidationError('Confirm password cannot be blank.');
	}

	public function signupWithWrongEmail(FunctionalTester $I)
	{
		$I->submitForm(
			$this->formId, [
			'SignupForm[username]'  => 'tester',
			'SignupForm[email]'	 => 'ttttt',
			'SignupForm[password]'  => 'tester_password',
			'SignupForm[password2]' => 'tester_password',
		]
		);
		$I->dontSee('Username cannot be blank.', '.help-block');
		$I->dontSee('Password cannot be blank.', '.help-block');
		$I->see('Email is not a valid email address.', '.help-block');
		$I->dontSee('Confirm password cannot be blank.', '.help-block');
	}

	public function signupWithUnmatchPassword(FunctionalTester $I) {
		$I->submitForm($this->formId, [
			'SignupForm[username]'  => 'tester',
			'SignupForm[email]'	 => 'ttttt@mail.com',
			'SignupForm[password]'  => 'tester_password',
			'SignupForm[password2]' => 'tester_pass',
		]);
		$I->dontSee('Username cannot be blank.', '.help-block');
		$I->dontSee('Password cannot be blank.', '.help-block');
		$I->dontSee('Email is not a valid email address.', '.help-block');
		$I->dontSee('Confirm password cannot be blank.', '.help-block');
		$I->see('Confirm password must be equal to "Password".', '.help-block');
	}

	public function signupUnavaliableNameAndEmail(FunctionalTester $I) {
		$I->submitForm($this->formId, [
			'SignupForm[username]'  => 'okirlin',
			'SignupForm[email]'	 => 'nicolas.dianna@hotmail.com',
			'SignupForm[password]'  => 'tester_pass',
			'SignupForm[password2]' => 'tester_pass',
		]);
		$I->see('This username has already been taken.', '.help-block');
		$I->submitForm($this->formId, [
			'SignupForm[username]'  => 'nyc',
			'SignupForm[email]'	 => 'nicolas.dianna@hotmail.com',
			'SignupForm[password]'  => 'tester_pass',
			'SignupForm[password2]' => 'tester_pass',
		]);
		$I->see('This email address has already been taken.', '.help-block');
	}

	public function signupWithShortPassword(FunctionalTester $I) {
		$I->submitForm($this->formId, [
			'SignupForm[username]'  => 'tester',
			'SignupForm[email]'	 => 'ttttt@mail.com',
			'SignupForm[password]'  => 'test',
			'SignupForm[password2]' => 'test',
		]);
		$I->dontSee('Username cannot be blank.', '.help-block');
		$I->dontSee('Password cannot be blank.', '.help-block');
		$I->dontSee('Email is not a valid email address.', '.help-block');
		$I->see('Password should contain at least 6 characters.', '.help-block');
		$I->dontSee('Confirm password cannot be blank.', '.help-block');
		$I->dontSee('Confirm password must be equal to "Password".', '.help-block');
	}

	public function signupSuccessfully(FunctionalTester $I)
	{
		$I->submitForm($this->formId, [
			'SignupForm[username]' => 'tester',
			'SignupForm[email]' => 'tester.email@example.com',
			'SignupForm[password]' => 'tester_password',
			'SignupForm[password2]' => 'tester_password',
		]);

		$I->seeRecord(User::class, [
			'username' => 'tester',
			'email' => null,
			'status' => User::STATUS_ACTIVE
		]);

		$I->seeEmailIsSent();
		$I->see('Thank you for registration. Please check your inbox for verification email.');
		$I->see('Logout (tester)');
	}
}
