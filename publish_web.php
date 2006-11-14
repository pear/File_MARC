<?php
$package      = 'File_MARC';// What is our package name?
$package_file = 'MARC.php'; // What is our primary package?
$publish_dir = "/home/dan/tmp/marcweb/"; // Publish directory

// What's the name of our HTML file?
$html_file = 'index.html';

// Clean out the directory
exec("rm -fr $publish_dir");

// Export the SVN repository 
exec("svn export . $publish_dir");

// Rest of our work will be done within the publish directory 
chdir($publish_dir);

// Define the set of files to highlight: package + examples
// Need to split this array into package file vs. example files somehow
$files_to_highlight = array($package_file => $package_file);
$examples = scandir('examples');
foreach ($examples as $example_file) {
  if ($example_file == '.' or $example_file == '..') { continue; }
  $files_to_highlight[$example_file] = "examples/$example_file";
}

// Create the package
exec('pear package');

// Create file highlighter php pages for package
foreach ($files_to_highlight as $highlighter => $path) {
    $fh = fopen("highlight_$highlighter", "w");
    fwrite($fh, "<?php highlight_file('$path'); ?>");
    fclose($fh);
}

// Create the online API documentation
exec("phpdoc -t docs -f $package_file");

// create php.ini files with short_open_tag = off in each doc subdirectory
// this is only required because ICDSoft turns on short_tags globally
// and phpDocumentor creates XHTML files with .php.html extensions
$doc_dir = scandir("docs/");
foreach ($doc_dir as $directory) {
    if (!is_dir("docs/$directory")) { continue; }
    if ($directory == '.' or $directory == '..') { continue; }
    $fh = fopen("docs/$directory/php.ini", "w");
    fwrite($fh, "short_open_tag = Off");
    fclose($fh);
}

// get new package name
$new_package = '';
$contents = scandir('.');
foreach ($contents as $file) {
    if (preg_match("/$package-[\d\.]+.tgz/", $file)) {
        $new_package = $file;
    }
}

// update HTML file with links to new package version
$html = file_get_contents($html_file);
$new_html = preg_replace("/current.tgz/", $new_package, $html);
$fh = fopen($html_file, 'w');
fwrite($fh, $new_html);
fclose($fh);

unlink('publish_web.php');

?>
