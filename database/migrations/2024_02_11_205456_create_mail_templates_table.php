<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 3)->index('mail_templates_lang_foreign');
            $table->string('key');
            $table->string('name');
            $table->string('subject');
            $table->longText('body');
            $table->longText('shortcodes')->nullable();
            $table->boolean('status')->default(true);
        });

        $mail_templates = array(
            array('lang' => 'en','key' => 'password_reset','name' => 'Reset Password','subject' => 'Reset Password Notification','body' => '<h2><strong>Hello!</strong></h2><p>You are receiving this email because we received a password reset request for your account, please click on the link below to reset your password.</p><p><a href="{{link}}">{{link}}</a></p><p>This password reset link will expire in <strong>{{expiry_time}}</strong> minutes. If you did not request a password reset, no further action is required.</p><p>Regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{
"link":"Password reset link",
"expiry_time":"Link expiry time",
"website_name":"Your website name"
}','status' => '1'),
            array('lang' => 'en','key' => 'email_verification','name' => 'Email Verification','subject' => 'Verify Email Address','body' => '<h2>Hello!</h2><p>Please click on the link below to verify your email address.</p><p><a href="{{link}}">{{link}}</a></p><p>If you did not create an account, no further action is required.</p><p>&nbsp;</p><p>Regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"link":"Email verification link","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'en','key' => 'subscription_about_expired','name' => 'Subscription About To Expired Notification','subject' => 'Your subscription is about to expire','body' => '<h2>Hi, <strong>{{username}}</strong></h2><p>We hope you\'re enjoying using our service. Just a friendly reminder that your subscription on <strong>{{plan}}</strong> plan is about to expire on <strong>{{expiry_date}}</strong>.</p><p>To continue receiving the benefits of our service, please renew your subscription before the expiration date. Simply follow the link below to access your account and renew:</p><p><a href="{{link}}">{{link}}</a></p><p>Our team is always here to assist you with any questions or concerns.</p><p>Thank you for being a valued customer.</p><p>Best regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"username":"User name","plan":"Subscription plan name","expiry_date":"Subscription expiry date","link":"User Subscription page","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'en','key' => 'subscription_expired','name' => 'Subscription Expired Notification','subject' => 'Your subscription has been expired','body' => '<h2>Hi, <strong>{{username}}</strong></h2><p>I hope this email finds you well. We wanted to let you know that your subscription on <strong>{{plan}}</strong> plan has expired on <strong>{{expiry_date}}</strong>.</p><p>We understand that life can get busy, but we would love the opportunity to continue serving you. If you renew your subscription now, you\'ll be able to take advantage of all the benefits and services we have to offer.</p><p>Renewing your subscription is easy, simply log into your account and select the plan that works best for you, or by clicking on the link below. If you have any questions or concerns, please don\'t hesitate to reach out to our customer support team.</p><p><a href="{{link}}">{{link}}</a></p><p>Please note that if you do not renew your subscription soon, it will be deleted permanently. We would hate to see you go, but if you do decide to let your subscription expire, please know that it has been a pleasure serving you.</p><p>Thank you for choosing us, and we hope to have the opportunity to serve you again soon.</p><p>Best regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"username":"User name","plan":"Subscription plan name","expiry_date":"Subscription expiry date","link":"User Subscription page","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'en','key' => 'subscription_deleted','name' => 'Subscription Deleted Notification','subject' => 'Your subscription has been deleted','body' => '<h2>Hi, <strong>{{username}}</strong></h2><p>We regret to inform you that your subscription on <strong>{{plan}}</strong> plan has been deleted due to its expiration.</p><p>We understand that you might still be interested in using our services, and we would be more than happy to welcome you back as a subscriber. If you have any questions or concerns, please don\'t hesitate to reach out to us.</p><p>Thank you for choosing us as your service provider, and we hope to have the opportunity to serve you again in the future.</p><p>Best regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"username":"User name","plan":"Subscription plan name","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'fr','key' => 'password_reset','name' => 'Reset Password','subject' => 'Reset Password Notification','body' => '<h2><strong>Hello!</strong></h2><p>You are receiving this email because we received a password reset request for your account, please click on the link below to reset your password.</p><p><a href="{{link}}">{{link}}</a></p><p>This password reset link will expire in <strong>{{expiry_time}}</strong> minutes. If you did not request a password reset, no further action is required.</p><p>Regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"link":"Password reset link","expiry_time":"Link expiry time","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'fr','key' => 'email_verification','name' => 'Email Verification','subject' => 'Verify Email Address','body' => '<h2>Hello!</h2><p>Please click on the link below to verify your email address.</p><p><a href="{{link}}">{{link}}</a></p><p>If you did not create an account, no further action is required.</p><p>&nbsp;</p><p>Regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"link":"Email verification link","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'fr','key' => 'subscription_about_expired','name' => 'Subscription About To Expired Notification','subject' => 'Your subscription is about to expire','body' => '<h2>Hi, <strong>{{username}}</strong></h2><p>We hope you\'re enjoying using our service. Just a friendly reminder that your subscription on <strong>{{plan}}</strong> plan is about to expire on <strong>{{expiry_date}}</strong>.</p><p>To continue receiving the benefits of our service, please renew your subscription before the expiration date. Simply follow the link below to access your account and renew:</p><p><a href="{{link}}">{{link}}</a></p><p>Our team is always here to assist you with any questions or concerns.</p><p>Thank you for being a valued customer.</p><p>Best regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"username":"User name","plan":"Subscription plan name","expiry_date":"Subscription expiry date","link":"User Subscription page","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'fr','key' => 'subscription_expired','name' => 'Subscription Expired Notification','subject' => 'Your subscription has been expired','body' => '<h2>Hi, <strong>{{username}}</strong></h2><p>I hope this email finds you well. We wanted to let you know that your subscription on <strong>{{plan}}</strong> plan has expired on <strong>{{expiry_date}}</strong>.</p><p>We understand that life can get busy, but we would love the opportunity to continue serving you. If you renew your subscription now, you\'ll be able to take advantage of all the benefits and services we have to offer.</p><p>Renewing your subscription is easy, simply log into your account and select the plan that works best for you, or by clicking on the link below. If you have any questions or concerns, please don\'t hesitate to reach out to our customer support team.</p><p><a href="{{link}}">{{link}}</a></p><p>Please note that if you do not renew your subscription soon, it will be deleted permanently. We would hate to see you go, but if you do decide to let your subscription expire, please know that it has been a pleasure serving you.</p><p>Thank you for choosing us, and we hope to have the opportunity to serve you again soon.</p><p>Best regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"username":"User name","plan":"Subscription plan name","expiry_date":"Subscription expiry date","link":"User Subscription page","website_name":"Your website name"}','status' => '1'),
            array('lang' => 'fr','key' => 'subscription_deleted','name' => 'Subscription Deleted Notification','subject' => 'Your subscription has been deleted','body' => '<h2>Hi, <strong>{{username}}</strong></h2><p>We regret to inform you that your subscription on <strong>{{plan}}</strong> plan has been deleted due to its expiration.</p><p>We understand that you might still be interested in using our services, and we would be more than happy to welcome you back as a subscriber. If you have any questions or concerns, please don\'t hesitate to reach out to us.</p><p>Thank you for choosing us as your service provider, and we hope to have the opportunity to serve you again in the future.</p><p>Best regards,<br><strong>{{website_name}}</strong></p>','shortcodes' => '{"username":"User name","plan":"Subscription plan name","website_name":"Your website name"}','status' => '1')
        );

        DB::table('mail_templates')->insert($mail_templates);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_templates');
    }
};
