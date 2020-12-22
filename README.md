<img src="/resources/favicon.ico" alt="Favicon" align="right">

# Hearts card game simulator

This is a Hearts [(AKA Black Lady)](https://en.wikipedia.org/wiki/Black_Lady)
card game simulator useable on the command line or in the browser. See
[RULES.md](RULES.md) for the hearts rules.  

## Requirements

The only requirements that need to be installed are PHP ^7.4 and Composer.

## Setup

    $ composer setup

## Unit tests

If you want to run the unit tests separately, run:

    $ composer test

## Usage

You can view the output of the simulation through a HTML-page or the command
line:  

##### HTML  

The HTML view uses the built in PHP Development Server. Run the command below
and visit `localhost:8000` in your browser.  

    $ composer hearts

##### Command Line  

If you use the command line you can step through, trick by trick, or run through
the simulation. Run through:  

    $ php index.php

Step through:  

    $ php index.php --step

## Possible approvements

* Create player strategies: Currently there is just one strategy, namely
  'Random'. A player can now only pick allowed cards at random. It would be good
  to have different players with different strategies. Or at least pick a low or
  high card at the appropriate moment and get rid of the Queen of Spades when
  possible.  
* Add strategy for passing cards. Now simply the highest cards are being passed.  
* Implement State pattern for `Trick` to simplify CardFilter generation in each
  possible state (eg.: opening, leading, following turns). This will improve and
  simplify `Game->playTrick()`.  
* Shooting the moon, when a player receives all penalty points in a trick the
  roles will be reversed and all *other* players get the maximum amount of penalty
  points instead. See [RULES.md](RULES.md).  
* The unit tests are in some cases dependent on other classes, this could be
  corrected. Some test code is also duplicate and could probably be moved to
  several fixtures.  
* A few unit tests are testing the internal workings of classes, this can be
  stripped to just test the behavior.  

## License
This project is under the MIT license. See [LICENSE](LICENSE) for more information.  
