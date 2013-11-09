<?php namespace Pyro\FieldType;

use Pyro\Module\Streams_core\Cp;
use Pyro\Module\Streams_core\Core\Model;
use Pyro\Module\Streams_core\Core\Field\AbstractField;

/**
 * PyroStreams Relationship Field Type
 *
 * @package		PyroCMS\Core\Modules\Streams Core\Field Types
 * @author		Parse19
 * @copyright	Copyright (c) 2011 - 2012, Parse19
 * @license		http://parse19.com/pyrostreams/docs/license
 * @link		http://parse19.com/pyrostreams
 */
class Relationship extends AbstractField
{
	/**
	 * Field type slug
	 * @var string
	 */
	public $field_type_slug = 'relationship';

	/**
	 * DB column type
	 * @var string
	 */
	public $db_col_type = 'integer';

	/**
	 * Custom parameters
	 * @var array
	 */
	public $custom_parameters = array(
		'stream',
		'placeholder',
		'value_field',
		'label_field',
		'search_field',
		'template',
		'module_slug',
		'relation_class',
		);

	/**
	 * Version
	 * @var string
	 */
	public $version = '2.0';

	/**
	 * Author
	 * @var  array
	 */
	public $author = array(
		'name' => 'Ryan Thompson - PyroCMS',
		'url' => 'http://pyrocms.com/'
		);


	/**
	 * Relation
	 * @return object The relation object
	 */
	public function relation()
	{
		return $this->belongsToEntry($this->getParameter('relation_class', 'Pyro\Module\Streams_core\Core\Model\Entry'));
	}

	/**
	 * Output form input
	 *
	 * @access 	public
	 * @return	string
	 */
	public function formInput()
	{
		// Start the HTML
		$html = form_dropdown($this->form_slug, array(), null, 'id="'.$this->form_slug.'" class="skip" placeholder="'.lang_label($this->getParameter('placeholder', 'lang:streams:relationship.placeholder')).'"');

		// Append our JS to the HTML since it's special
		$html .= $this->view(
			'fragments/relationship.js.php',
			array(
				'form_slug' => $this->form_slug,
				'field_slug' => $this->field->field_slug,
				'stream_namespace' => $this->stream->stream_namespace,
				'stream_param' => $this->getParameter('stream'),
				'value_field' => $this->getParameter('value_field', 'id'),
				'label_field' => $this->getParameter('label_field', '_title_column'),
				'search_field' => $this->getParameter('search_field', '_title_column'),
				'value' => $this->getValueEntry(),
				),
			false
			);

		return $html;
	}

	/**
	 * Output filter input
	 *
	 * @access 	public
	 * @return	string
	 */
	public function filterInput()
	{
		// Start the HTML
		$html = form_dropdown($this->getFilterSlug('is'), array(), null, 'id="'.$this->getFilterSlug('is').'" class="skip" placeholder="'.$this->field->field_name.'"');

		// Append our JS to the HTML since it's special
		$html .= $this->view(
			'fragments/relationship.js.php',
			array(
				'form_slug' => $this->getFilterSlug('is'),
				'field_slug' => $this->field->field_slug,
				'stream_namespace' => $this->stream->stream_namespace,
				'stream_param' => $this->getParameter('stream'),
				'value_field' => $this->getParameter('value_field', 'id'),
				'label_field' => $this->getParameter('label_field', 'id'),
				'search_field' => $this->getParameter('search_field', 'id'),
				'value' => $this->filterRelationResults(ci()->input->get($this->getFilterSlug('is'))),
				),
			false
			);

		return $html;
	}

	/**
	 * Pre Ouput
	 *
	 * Process before outputting on the CP. Since
	 * there is less need for performance on the back end,
	 * this is accomplished via just grabbing the title column
	 * and the id and displaying a link (ie, no joins here).
	 *
	 * @return	mixed 	null or string
	 */
	public function stringOutput()
	{
		if ($entry = $this->getRelationResult())
		{
			return $entry->getTitleColumnValue();
		}

		return null;
	}

	/**
	 * Pre Ouput Plugin
	 * 
	 * This takes the data from the join array
	 * and formats it using the row parser.
	 * 
	 * @return array
	 */
	public function pluginOutput()
	{
		if ($entry = $this->getRelationResult())
		{
			return $entry->toArray();
		}

		return null;
	}

	///////////////////////////////////////////////////////////////////////////////
	// -------------------------	PARAMETERS 	  ------------------------------ //
	///////////////////////////////////////////////////////////////////////////////

	/**
	 * Choose a stream to relate to.. or remote source
	 * @param  mixed $value
	 * @return string
	 */
	public function paramStream($value = '')
	{
		$options = Model\Stream::getStreamAssociativeOptions();

		return form_dropdown('stream', $options, $value);
	}

	/**
	 * Define the placeholder of the input
	 * @param  string $value
	 * @return html
	 */
	public function paramPlaceholder($value = '')
	{
		return form_input('placeholder', $value);
	}

	/**
	 * Define the field to use for values
	 * @param  string $value
	 * @return html
	 */
	public function paramValueField($value = '')
	{
		return form_input('value_field', $value);
	}

	/**
	 * Define the field to use for labels (options)
	 * @param  string $value
	 * @return html
	 */
	public function paramLabelField($value = '')
	{
		return form_input('label_field', $value);
	}

	/**
	 * Define the field to use for search
	 * @param  string $value
	 * @return html
	 */
	public function paramSearchField($value = '')
	{
		return form_input('search_field', $value);
	}

	/**
	 * Define any special template slug for this stream
	 * Loads like:
	 *  - views/field_types/TEMPLATE/option.php
	 *  - views/field_types/TEMPLATE/item.php
	 * @param  string $value
	 * @return html
	 */
	public function paramTemplate($value = '')
	{
		return form_input('template', $value);
	}

	/**
	 * Define an override of the module slug
	 * in case it is not the same as the namespace
	 * @param  string $value
	 * @return html
	 */
	public function paramModuleSlug($value = '')
	{
		return form_input('module_slug', $value);
	}

	/**
	 * Define an override of the module slug
	 * in case it is not the same as the namespace
	 * @param  string $value
	 * @return html
	 */
	public function paramRelationClass($value = '')
	{
		return form_input('relation_class', $value);
	}

	///////////////////////////////////////////////////////////////////////////
	// -------------------------	AJAX 	  ------------------------------ //
	///////////////////////////////////////////////////////////////////////////

	public function ajaxSearch()
	{
		/**
		 * Determine the stream
		 */
		$stream = explode('.', ci()->uri->segment(7));
		$stream = Model\Stream::findBySlugAndNamespace($stream[0], $stream[1]);


		/**
		 * Determine our field / type
		 */
		$field = Model\Field::findBySlugAndNamespace(ci()->uri->segment(8), ci()->uri->segment(6));
		$field_type = $field->getType(null);


		/**
		 * Get our fields for the select
		 */
		
		$fields = array_unique(
			array(
				$field_type->getParameter('value_field', 'id'),
				$field_type->getParameter('label_field'),
				$field_type->getParameter('search_field'),
				)
			);

		$entries = Model\Entry::stream($stream->stream_slug, $stream->stream_namespace)->select($fields)->where($field_type->getParameter('search_field'), 'LIKE', '%'.ci()->input->get('query').'%')->take(10)->get();


		/**
		 * Stash the title_column just in case nothing is defined later
		 */
		$entries = $entries->asEloquent()->toArray();

		header('Content-type: application/json');
		echo json_encode(array('entries' => $entries));
	}

	///////////////////////////////////////////////////////////////////////////////
	// -------------------------	UTILITIES 	  ------------------------------ //
	///////////////////////////////////////////////////////////////////////////////

	protected function getValueEntry()
	{
		// Break apart the stream
		$stream = explode('.', $this->getParameter('stream'));
		$stream = Model\Stream::findBySlugAndNamespace($stream[0], $stream[1]);

		// Boom
		return $this->getRelationResult();
	}

	/**
	 * Get the results for the field type relation
	 * @return [type] [description]
	 */
	protected function filterRelationResults($value)
	{
		$field_slug = $this->field->field_slug;
		
		// Check if we have value
		$original = $value;

		if ($this->hasLocalForeingKey() and empty($original)) return null;

		// If the relation result exists, return it
		if ($relation = $this->entry->getRelation($field_slug)) {
			
			return $relation;
		
		} elseif ($this->hasRelation()) {
		
			return $this->relation()->getResults();
		
		}

		return null;
	}
}
