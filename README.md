# hashdir
Encrypted directory class to buy the source send $150 to Paypall info@climatebabes.com
<p>
This class saves data in encrypted files named by hashvalues that for the most part are unknown. Data can be accessed by linux style dir commands and magic functions for getting and setting (as shown below). This is ideal for keeping records you yourself don't even want to access, and which you don't want any hacked to bave much use of. 
<br>
Most important future change is automatic file size, so large directories get split up so it is not possible to recognize a file by its size. 
<br>
Example encrypted data:
<pre>xn��n���%�8�O�-��t��c�6�0�N>��c2��|F�R
�8�T�Gu�`�{�)�fD�<�}�<��������z�A��D���4���3�(��+���оA�v
</pre>
</p>
<p>
Create a hashdir using a key, a hash (will be the file name) and a root name.
</p>
<pre>
$hashdir = new Hashdir('4b17d3264f','234833147b97bb6aed53a8f4f1c7a7d8','framboos');
</pre>
<p>
Dump a listing of the elements of the hashdir
</p>
<pre>
$hashdir->ls();
</pre>
<p>
Output shows hashdir name, values and in case of a subhashdir the hash (which is the filename the data is stored in)
</p>
<pre>
hashdir :framboos
biercoin 111
sub1 hashdir d840cc5d906c3e9c84374c8919d2074e
sub2 hashdir 35d8f387d4934b6ee53ce5c9a1d8c1d7
</pre>
<p>
To get a value of this $hashdir use 
</p>
<pre>
  $hashdir->varname;
</pre>
<p>
This can either return an value, an array <b>or a hashdir object of the subdir</b>. So you can drill down into your hashdirectories like this:
<pre>
$subdir = $hashdir->sub1;
$subdir->ls();
$subdir->variable = 10;
</pre>
Newly set values are stored in encrypted form instantly. This only touches the part of the directory tree contained by that hashdir object, so it is fast. 
</p>
<p>
To assign a value in this hashdir use 
</p>
<pre>
  $hashdir->varname = value (array or value);
</pre>
<p>
This sets the array element by the name varname to value.
</p>
To create a subdir use 
</p>
<pre>
$hashdir->mkdir("sub3",array(1,4423,23,145,6));
</pre>

<p>
Functions for distributed storage of hashdir files, private and public key use and maintenance functions are in the works.
</p>


