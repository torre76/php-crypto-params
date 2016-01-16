Documentation
=============

Installation
------------

This storage is hosted on `Packagist <https://packagist.org/packages/torre76/php-crypto-params>`_. It can be easily installed
configuring `Composer <https://getcomposer.org/>`_:

.. code-block:: json

  {
      "require": {
          "torre76/php-crypto-params": "1.0.*"
      }
  }

Usage
-----

To initialize the encryption - decryption system, the ``\\CryptoParams\\CryptoParams`` class is used:

.. code-block:: php

  <?php
  require __DIR__ . '/vendor/autoload.php';

  $cp = new \CryptoParams\CryptoParams();


The initialization without parameters auto generate a 32 bytes key and a 32 bytes initialization vector (as per
`AES <https://en.wikipedia.org/wiki/Advanced_Encryption_Standard>`_ specification).

The generated values are available through these properties:

- ``key``
- ``iv``

``\\CryptoParams\\CryptoParams`` class accept custom *key* and *initialization vector* though the
properties above and using the constructor:

.. code-block:: php

  <?php
  require __DIR__ . '/vendor/autoload.php';

  $cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");

The requisites to use custom *key* and *initialization vector* are:

- **key** must be a 32 bytes string written in hexadecimal base (it is not meant to be human readable)
- **initialization vector** must be a 32 bytes string written in hexadecimal base (it is not meant to be human readable)

If those requirements are not met a ```\\CryptoParams\\CryptoParamsException``` exception will be raised.

Once the class has been initialized, a string could be encrypted using
``encrypt(value)`` method:

.. code-block:: php

    <?php
    require __DIR__ . '/vendor/autoload.php';

    $cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
    $encrypted = $cp->encrypt("aieiebrazorf");

    // $encrypted contains "iW8qzzEWpWRN0NPNoOwu3A=="


This function returns a **Base64 encoded string** ready to be used into query strings.

To decrypt a **Base64 encoded string** with data the method used is
``decrypt(value)``:

.. code-block:: php

    <?php
    require __DIR__ . '/vendor/autoload.php';

    $cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
    $decrypted = $cp->decrypt("iW8qzzEWpWRN0NPNoOwu3A==");

    // $decrypted contains "aieiebrazorf"

It is possibile to encrypt and decrypt complex data transofming them into string such as *JSON*. Everything that can be serialized to a string can be encrypted and decrypted:

.. code-block:: php

    <?php
    require __DIR__ . '/vendor/autoload.php';

    $cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
    $data = array();
    $data["id"] = 1;
    $data["description"] = "Description";

    $buffer = json_encode($data);
    $encrypted = $cp->encrypt($buffer);
    
    $buffer = $cp->decrypt($encrypted);
    $data = json_decode($buffer, FALSE);

    // $data->id contains 1
    // $data->description contains "Description"

Source and License
------------------

Source can be found on `GitHub <https://github.com/torre76/php-crypto-params>`_ with its included
`license <https://raw.githubusercontent.com/torre76/php-crypto-params/master/LICENSE.txt>`_.