php-crypto-params
=================

Utility function to encrypt - decrypt string using AES symmetric algorithm that is compatible with
`crypto-js <https://code.google.com/p/crypto-js/>`_.

Purpose
-------

Harvesting data on the web has become an easy task.

Often, to obtain data stored into a database, a simple script loops on a numeric query parameter
(called usually *id*) embedded into an *URL* and it donwloads a lot of useful data.

Another weakness on sites are *Javascript config files* that holds *JSON* with valuable data.

Last but not least, *AJAX call* contains a lot of information and, if unprotected, they can easily looped to obtain
all their contents.

How to prevent these flaws?
Maybe if the query string or the data is encrypted a lot of those scripts will not work...

How it works
------------

The `\\CryptoParams\\CryptoParams` class provide methods to encrypt and decrypt strings using
`AES <https://en.wikipedia.org/wiki/Advanced_Encryption_Standard>`_ algorithm [#aesalg]_.
This way query parameters (but also *JSON responses*) can be obfuscated and read only by the possessors of the
encryption key.

This particular implementation, inspired by `marcoslin gist <https://gist.github.com/marcoslin/8026990>`_ is
compatible with `crypto-js <https://code.google.com/p/crypto-js/>`_ [#futurejs]_ ; this mean that a parameter encoded by a
*HTTP server* could be read by *Javascript*. The only caveat is to share (or at least to obfuscate) the key (and the
initialization vector) in a safely manner.

If the parameter is only on query string, only the server can translate them (since the key is not exposed), avoiding
obnoxious looping scripts that harvest the data.

.. rubric:: Footnotes

.. [#aesalg] AES is a symmetric encryption - decryption algorithm based on a 32 bytes shared key
    (and a shared *Initialization Vector*) that can obfuscate parameters and data.
.. [#futurejs] Starting from this GIST, sooner I will implement the *Javascript version of this algorithm* to allow
    the reading of data sent from the server directly in HTML pages.



.. toctree::
   :maxdepth: 1
   :hidden:

   Index <self>
   docs