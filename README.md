# Adding a Click to Call button to your Laravel Application

In this tutorial, we will be building a simple Laravel application that allows user's place automated calls using the [Twilio Programmable Voice](https://www.twilio.com/voice) API.

## Prerequisites

To  follow through with this tutorial, you will need the following:

- Basic knowledge of Laravel
- [Laravel](https://laravel.com/docs/master) Installed on your local machine
- [Composer](https://getcomposer.org/) globally installed
- [Twilio Account](https://www.twilio.com/referral/B2YAW1)

## Project Setup

Get started by first creating a new [Laravel](https://laravel.com/) project using the [Laravel installer](https://laravel.com/docs/7.x#installing-laravel). Open up a terminal and run the following:

    $ laravel new twilio-call-button

**NOTE:** *You need to have the Laravel installer already installed on your local machine. If you don't, then head over to the [official documentation](https://laravel.com/docs/7.x) to see how to get it installed.*

Next, the [Twilio PHP SDK](https://www.twilio.com/docs/libraries/php) is required to make API request to the Twilio servers. Open up a terminal in your project directory (*`twilio-call-button`*) and run the following command to get it installed via [Composer](https://getcomposer.org/):

    $ composer require twilio/sdk

To authenticate and identify requests made from your application using the Twilio SDK, your *account sid* and *auth token* are needed to initialize the Twilio SDK. So head to your [Twilio console](https://www.twilio.com/console) to copy both credentials:

![https://res.cloudinary.com/brianiyoha/image/upload/v1584495960/Articles%20sample/Group_18.png](https://res.cloudinary.com/brianiyoha/image/upload/v1584495960/Articles%20sample/Group_18.png)

Next, you will also need to copy out your [active twilio phone number](https://www.twilio.com/console/phone-numbers/incoming) which will be used for placing outgoing calls:

![https://res.cloudinary.com/brianiyoha/image/upload/v1584496232/Articles%20sample/Group_2_2.png](https://res.cloudinary.com/brianiyoha/image/upload/v1584496232/Articles%20sample/Group_2_2.png)

Now, safely store your credentials in your [environment variable](https://laravel.com/docs/7.x/configuration#environment-configuration).  Open up the `.env` file and add the following variables:

    TWILIO_SID="YOUR ACCOUNT SID"
    TWILIO_AUTH_TOKEN="YOUR ACCOUNT AUTH TOKEN"
    TWILIO_NUMBER="YOUR TWILIO NUMBER"

## Getting User' Phone Number

To enable you to place an outbound call using the Twilio programmable voice API you must provide a valid [E.164](https://www.twilio.com/docs/glossary/what-e164) phone number to the Twilio SDK. And one way to do this is by collecting phone numbers from your frontend using a form. Now open up  `resources/views/welcome.blade.php`  and replace its content with the following to add a form for collecting a user phone number:

    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <title>Laravel</title>
    
        <!-- Fonts -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
            integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <!-- Styles -->
        <style>
            html,
            body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
    
            .full-height {
                height: 100vh;
            }
    
            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }
    
            .position-ref {
                position: relative;
            }
    
            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
    
            .content {
                text-align: center;
            }
    
            .title {
                font-size: 84px;
            }
    
            .links {
                margin-top: 60px
            }
    
            .links>a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
    
            .m-b-md {
                margin-bottom: 30px;
            }
    
            label {
                font-weight: 600;
                font-size: 13px;
            }
    
            .btn-primary {
                color: #fff;
                background-color: #636a6e;
                border-color: #545b62;
                font-weight: 600;
            }
    
            .btn-primary:hover {
                color: #545b62;
                background-color: #fff;
                border-color: #545b62;
                font-weight: 600;
            }
        </style>
    </head>
    
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
            <div class="top-right links">
                @auth
                <a href="{{ url('/home') }}">Home</a>
                @else
                <a href="{{ route('login') }}">Login</a>
    
                @if (Route::has('register'))
                <a href="{{ route('register') }}">Register</a>
                @endif
                @endauth
            </div>
            @endif
    
            <div class="content">
                <div class="title m-b-md">
                    Laravel
    
                </div>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (isset($message))
                <div class="alert alert-success">
                    <ul>
                        <li>{{ $message }}</li>
                    </ul>
                </div>
                @endif
                <form action="{{route('placeCall')}}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label for="phoneNumber" class="col-sm-2 col-form-label">Phone Number</label>
                        <div class="col-sm-10">
                            <input type="tel" name="phoneNumber" class="form-control" id="phoneNumber"
                                placeholder="Phone number">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Call Me</button>
                </form>
                <div class="links">
                    <a href="https://twilio.com/blog">Twilio Blog</a>
                    <a href="https://github.com/thecodearcher/laravel-click-to-call">GitHub</a>
                </div>
            </div>
        </div>
    </body>
    
    </html>

**NOTE:** *[Bootstrap](https://getbootstrap.com/) is used to help expedite the styling of the form.*

The code above adds a form with an input field for collecting a user's phone number. After submission, the phone number collected will be submitted to a [named route](https://laravel.com/docs/7.x/routing#named-routes) `placeCall` - (which will be created in the later section of this tutorial) - which will place a call to the number entered with a message containing a pseudo confirmation pin.

## Making Outbound Calls

First, generate a [Controller](https://laravel.com/docs/7.x/controllers#introduction) class where the application logic for making a call will be included. Open up a terminal in the project directory and run the following command:

    $ php artisan make:controller HomeController

Next, open the newly create file (`app/Http/Controllers/HomeController.php`) and make the following changes:

    <?php
    
    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use Twilio\Rest\Client;
    use Twilio\TwiML\VoiceResponse;
    
    class HomeController extends Controller
    {
        public function index()
        {
            return view('welcome');
        }
    
        public function placeCall(Request $request)
        {
            $request->validate([
                'phoneNumber' => 'required|numeric',
            ]);
    
            $to_number = $request->input('phoneNumber');
    
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_number = getenv("TWILIO_NUMBER");
    
            $client = new Client($account_sid, $auth_token);
    
            $pin = implode('. ', str_split(rand(10000, 99999)));
    
            $client->account->calls->create(
                $to_number,
                $twilio_number,
                [
                    "twiml" => $this->callMessage($pin),
                ]
            );
            return view('welcome', ['message' => 'You will receive a call shortly!']);
        }
    
        public function callMessage($pin)
        {
            $response = new VoiceResponse();
            $response->say("Your Laravel test pin is: $pin.");
            $response->say("GoodBye!");
            $response->hangup();
            return $response;
        }
    }

Three new methods - `index()`, `placeCall()` and `callMessage()` has been added to the controller class. The `index()` method simply returns the `resources/views/welcome.blade.php` [view](https://laravel.com/docs/7.x/views). The `placeCall()` method as the name indicates, handles placing a call to a phone number with is gotten from the `phoneNumber` property on the request body. It makes use of the Twilio SDK to place a call by first initializing a new instance of the Twilio Client using the credentials stored as environment variables in the earlier section of this tutorial:

     $account_sid = getenv("TWILIO_SID");
     $auth_token = getenv("TWILIO_AUTH_TOKEN");
     $twilio_number = getenv("TWILIO_NUMBER");
    
     $client = new Client($account_sid, $auth_token);

Next, a five (5) digit pseudo pin is generated using the built-in `[rand()](https://www.php.net/manual/en/function.rand.php)` function in PHP:

     $pin = implode('. ', str_split(rand(10000, 99999)));

**NOTE:** *The generated* pseudo *pin is converted to an array using `[str_split()](https://www.php.net/manual/en/function.str-split)` and later turned back to a string using the `[implode()](https://www.php.net/manual/en/function.implode)` function which joins each element together by adding a period (.) and white space between each digit (element). This is to ensure Twilo TwiML reads each digit as an individual character.*

Next, using the chained `account->calls->create()` method of the Twilio client instance, an outbound call is made to the phone number sent in from the form:

      $client->account->calls->create(
                $to_number,
                $twilio_number,
                [
                    "twiml" => $this->callMessage($pin),
                ]
            );

The `account->calls->create()` method takes on three arguments; `to`, `from` and an `array` of [properties](https://www.twilio.com/docs/voice/api/call-resource#create-a-call-resource) to pass to the Twilio programmable voice API. As the names indicate; `to` and `from` are both the phone number you want to call and your Twilio phone number respectively. Twilio allows passing [different properties](https://www.twilio.com/docs/voice/api/call-resource#create-a-call-resource) to configure how your call will behave and in this tutorial, the `twiml` property is used. The `twiml` property allows passing in direct [TwiML](https://www.twilio.com/docs/voice/twiml) instructions which will be executed when the call is answered. To make things neater, the construction of the TwiML instructions is done in a separate function `callMessage()` which is passed as the value for `twiml`. The `callMessage()` method makes use of the `VoiceResponse()` class which is a helper class from the Twilio SDK to construct the needed XML markup which will be played to the user when the call is answered:

    public function callMessage($pin)
        {
            $response = new VoiceResponse();
            $response->say("Your Laravel test pin is: $pin.");
            $response->say("GoodBye!");
            $response->hangup();
            return $response;
        } 

Looking at the above snippet, a new `VoiceResponse()` instance is instantiated and further used to construct the [Say](https://www.twilio.com/docs/voice/twiml/say) verb using the fluent `say()` method. The `say()` method takes in a string that will read out the user when call is answered. Next, the [Hangup](https://www.twilio.com/docs/voice/twiml/hangup) verb is added which will end the call immediately the message has been read to the user. 

Next, the user will be returned to the `welcome` view with a `message` to inform them that their call request was successful:

         return view('welcome', ['message' => 'You will receive a call shortly!']);

## Registering Routes

Next, you have to create routes that will be used to access your application and also where data from the form which was created earlier will be submitted to. Open up `routes/web.php` and make the following changes:

    <?php
    
    use Illuminate\Support\Facades\Route;
    
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
    
    Route::get('/','HomeController@index')->name('home');
    Route::post('/','HomeController@placeCall')->name('placeCall'); 

## Testing

Awesome! Now that you have finished building your application, you can now proceed to test out your implementation. To get started, you need to first start up your Laravel application. To do this open up a terminal in your working directory and run the following command:

    $ php artisan serve

You should see a message like "Laravel development server started: [http://127.0.0.1:8000](http://127.0.0.1:8000/)" printed out on the terminal. Now open up your browser and navigate to the URL printed out, usually [http://127.0.0.1:8000](http://127.0.0.1:8000/) and you should be greeted with a page similar to this: 

![https://res.cloudinary.com/brianiyoha/image/upload/v1584587967/Articles%20sample/Screenshot_from_2020-03-19_04-18-48.png](https://res.cloudinary.com/brianiyoha/image/upload/v1584587967/Articles%20sample/Screenshot_from_2020-03-19_04-18-48.png)

Next, enter a valid phone number into the form field provided and click on the *Call Me* button and you should get a call shortly after reading out a pseudo pin to you.

## Conclusion

Now that you have finished this tutorial, you have learned how to implement a click-to-call button in a Laravel application using the Twilio Programmable Voice API. And with it, you have also learned how to construct TwiML instructions using the helper methods provided by the Twilio SDK. If you will like to take a look at the complete source code for this tutorial, you can be find it on [Github](https://github.com/thecodearcher/laravel-click-to-call).

I’d love to answer any question(s) you might have concerning this tutorial. You can reach me via

- Email: [brian.iyoha@gmail.com](mailto:brian.iyoha@gmail.com)
- Twitter: [thecodearcher](https://twitter.com/thecodearcher)
- GitHub: [thecodearcher](https://github.com/thecodearcher)
