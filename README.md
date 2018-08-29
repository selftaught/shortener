# Url Shortener (PHP >= 7.0)

## Task 1
Create a URL shortener (a much simpler version of a service like bit.ly), with the following features:
* Each entry must have **at least** the following fields: created_at, longurl
* All entries must be stored in **one** text (not binary) file
* The project **must** support the following functionality:
  * Shorten functionality
  * Get long URL by short URL

### Task 1.1
Create a simple CLI script with the following features:
* Shorten a url and display it (e.g. php ./cli.php --shorten "http://www.domain.tld")
* Display long url by searching given the short one (e.g. php ./cli.php --search "http://SHORT_URL")

### Task 1.2
Use PHPUnit to write tests for the URL shortener
