<?php

function odt2text($filename)
{
    $dataFile = 'content.xml';

    // Create a new ZIP archive object
    $zip = new ZipArchive;

    // Open the archive file
    if ($zip->open($filename) === true) {
        // If successful, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // Index found! Now read it to a string
            $text = $zip->getFromIndex($index);
            // Load XML from a string
            // Ignore errors and warnings
            $xml = DOMDocument::loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);

            // Remove XML formatting tags and return the text
            return strip_tags($xml->saveXML());
        }
        // Close the archive file
        $zip->close();
    }

    // In case of failure return a message
    return '';
}
