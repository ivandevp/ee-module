<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Auction {

	public $return_data;

	// Constructor
	public function __construct() 
	{
		$this->EE =& get_instance();
	}

	/**
	 * The module's Control Panel default controller
	 *
	 * @return void
	 */
	public function index()
	{

	}

	public function summary()
	{
		$tagdata = $this->EE->TMPL->tagdata;
		
		// Find the entry_id of the auction to display
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		if ($entry_id === "" || $entry_id === FALSE) {
			return "";
		}

		/*
		$tagdata = str_replace(LD . "current_bid" . RD, "0.00", $tagdata);
		$tagdata = str_replace(LD . "total_bids" . RD, "0", $tagdata);
		*/

		// Fetch data from database
		$this->EE->db->select("MAX(bid_amount) as current_bid, COUNT(*) as total_bids");
		$this->EE->db->where("entry_id", $entry_id);
		$this->EE->db->group_by("entry_id");
		$query = $this->EE->db->get("auction");

		if ($query->num_rows() == 0) {
			// No bids exist in the table, so we'll return zeros
			$data = array(
				"current_bid" => "0.00",
				"total_bids" => 0
			);
		} else {
			// Fetch the first (and only) result from the SQL query
			$data = $query->row_array();
		}

		// construct $variables array for use in parse_variables method
		$variables = array();
		$variables[] = $data;
		
		return $this->EE->TMPL->parse_variables($tagdata, $variables);
	}

	public function form()
	{
		// Find the entry_id of the auction to add the form for
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		if ($entry_id === "" || $entry_id === FALSE) {
			return "";
		}

		// Build an array to hold the form's hidden fields
		$hidden_fields = array(
			"entry_id" => $entry_id,
			"ACT" => $this->EE->functions->fetch_action_id('Auction', 'place_bid')
		);

		// Build an array with the form data
		$form_data = array(
			"id" => $this->EE->TMPL->form_id,
			"class" => $this->EE->TMPPL->form_class,
			"hidden_fields" => $hidden_fields
		);

		// Fetch contents of the tag pair, ie, the form contents
		$tagdata = $this->EE->TMPL->tagdata;

		$form = $this->EE->functions->form_declaration($form_data) . $tagdata . "</form>";

		return $form;
	}

	public function place_bid() {
		$entry_id = $this->EE->input->post('entry_id', TRUE);
		$bid_amount = $this->EE->input->post('bid_amount', TRUE);
		$member_id = $this->EE->session->userdata('member_id');
		$bid_date = $this->EE->localize->now;

		$data = array(
			"entry_id" => $entry_id,
			"member_id" => $member_id,
			"bid_amount" => $bid_amount,
			"bid_date" => $bid_date
		);

		$this->EE->db->insert('auction', $data);

		$ret = $this->EE->functions->fetch_site_index();

		$data = array(
			'title' => 'Thanks for your bid',
			'heading' => 'Thanks for your bid',
			'content' => 'Your bid has been successfully placed!',
			'link' => array($ret, "Back to site")
		);

		return $this->EE->output->show_message($data);
	}

}