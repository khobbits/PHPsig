PHPsig
======

Dynamic rotating signature created in PHP using GD libraries

Excuses
-----------
I was asked to make this bit of code available for learning/demo purposes.<br />
I don't particularly think it is a good example of PHP programming, but it does demo a few little .htaccess tricks, along with some examples of how to parse MAL data, and also use of the GD libraries, so on the whole, I think it is worth while to publish this.<br />
You will have to excuse my PHP, I wrote this in 2008, when I was just learning how to use the GD libraries properly, and more time was spent making it work, than making it tidy.

Install
-----------
Make sure you have php with gd libraries.<br />
Make sure you have the php curl library<br />
Create a folder called 'mal_cache' thats script writeable<br />
Make sure the genlog.txt file is script writeable<br />
Configure the account name to poll in the sig.php (and maybe the debug.php if you want that).

Contributions
-----------
I'm not expecting much in the way of feedback or corrections to this code.  I'm posting it here primarily for display purposes.  If you do decide to edit/improve the code, feel free to drop me a pull request.

Location
-----------
This code runs at: http://s.khhq.net<br />
This code is located at: https://github.com/khobbits/PHPsig