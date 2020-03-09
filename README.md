# PSR-3 logger for Journald

An experimental PSR-3 compatible logger logging (structured) data to Journald.

## Requirements

This library requires PHP 7.4 because it uses
[FFI](https://www.php.net/manual/en/class.ffi.php) to write to
Journald.

You also need to have systemd on the machine.

## Usage

See [example.php](example.php).

## TODO

 * [ ] Handle placeholders in messages
 * [ ] Add `CODE_FUNC`, `CODE_LINE`, and `CODE_FILE`
 * [ ] Behave well when no systemd is present
 * [ ] Documentation
 * [ ] Tests
