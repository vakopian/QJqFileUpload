<?php
require('jq_control.php');

class JqUploaderDoc extends JqDoc {

	public function __construct($strUrl)
	{
		$this->hasDisabledProperty = true;
		$html = file_get_html($strUrl);

		parent::__construct('FileUpload', 'fileupload', 'QJqFileUpload', 'QFileControl');

		/** @var simple_html_dom_node[] $nodes */
		$nodes = $html->find('div.markdown-body h3');

		foreach ($nodes as $node) {
			$name = trim($node->plaintext);
			if ($name == 'url') {
				// URL is special, we don't want it to be customized by caller
				continue;
			}
			$sibling = $node->next_sibling();
			$description = '';
			for (; $sibling && $sibling->tag == 'p'; $sibling = $sibling->next_sibling()) {
				$description .= $sibling->outertext() . "\n";
			}
			for (; $sibling && $sibling->tag !== 'ul'; $sibling = $sibling->next_sibling()) {
			}
			$type = null;
			if ($sibling != null) {
				$lis = $sibling->find('li em');
				if ($lis) {
					$type = trim($lis[0]->plaintext);
					if ($type) switch ($type) {
						case 'jQuery Object':
							$type = 'object';
							break;
						case 'Regular Expression':
							$type = 'string';
							break;

					}
				}
			}
			$origName = $name;
			$name = $this->unique_name($name);
			if ($type) {
				$this->options[] = new Option($name, $origName, $type, null, $description);
			} else {
				$this->options[] = new Option($name, $origName, 'function', null, $description);
				$this->events[] = new Event('QJqFileUpload', $name, $origName, 'fileupload'.$name, null, $description);
			}
		}
	}
}

$jqControlGen = new JqControlGen();
$jqControlGen->GenerateControl(new JqUploaderDoc('https://github.com/blueimp/jQuery-File-Upload/wiki/Options'));

?>