@extends('layouts.layout')
@section('content')
    <div class="content">
        <table>
            <tr>
                <td>
                    <h3 style="color: #41a2d5; font-weight: 500;">Welcome to the Social Media Income Family, {{ $user->name }}!</h3>
                    <p class="lead">We are very excited to have you as part of the team!</p>
                    <p>As an initial contributor of the "SMI" platform, you will be entitled to special benefits. Some of these benefits include early access to the social media income platform and special monthly "SMI" pool access. You can learn more about the early contributor benefits on our website or through our SMI community.</p>

                    {{--<p style="background: #262f3e; border: 1px solid #293444; padding: 20px;">--}}
                        {{--Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra vitae. <a href="#" style="color: #F8F8F8; text-decoration: underline; font-weight: 400;">Click it! &raquo;</a>--}}
                    {{--</p>--}}

                    <p>We really appreciate your interest in SMI and will do our best to make it worth your while!</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>Sincerely, <br> Social Media Income Team</p>

                    <table class="social" style="border-top: 1px solid rgba(120, 130, 140, 0.13); margin-top: 40px;" width="100%">
                        <tr>
                            <td>

                                <table align="left" class="column">
                                    <tr>
                                        <td>
                                            <h5 class="">Connect with Us:</h5>
                                            <p class="">
                                                <a href="https://www.facebook.com/smicoin" class="soc-btn fb"><img src="https://s3.amazonaws.com/socialmediaincome.com/images/fb-icon.png" alt="Social Media Income Facebook" width="50"></a>
                                                <a href="https://twitter.com/smicoin" class="soc-btn tw"><img src="https://s3.amazonaws.com/socialmediaincome.com/images/twitter-icon.png" alt="Social Media Income Twitter" width="50"></a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <table align="left" class="column">
                                    <tr>
                                        <td>
                                            <h5 class="">Contact Info:</h5>
                                            <p>Support Email:<br> <a  href="mailto:support@socialmediaincome.com" style="color: #F8F8F8; text-decoration: none; font-weight: 300; padding: 10px 0;">support@socialmediaincome.com</a></p>
                                        </td>
                                    </tr>
                                </table>
                                <span class="clear"></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection