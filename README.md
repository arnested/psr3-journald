# PSR-3 logger for Journald

An experimental PSR-3 compatible logger logging (structured) data to Journald.

## Requirements

This library requires PHP 8.1 or later.

[FFI](https://www.php.net/manual/en/class.ffi.php) is supported from
7.4 and is used to write to Journald.

You also need to have systemd on the machine.

## Usage

See [example.php](example.php).
