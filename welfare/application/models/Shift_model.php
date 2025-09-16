<?php //if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/models/Base_model.php';

class Shift_model extends Base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tbl_shift';
        $this->primary_key = 'shift_id';
    }

    function getShiftByCond($cond_date)
    {
        $this->db->select('Staff.staff_id, Staff.staff_name, Staff.company_id, Shift.shift_date, Shift.shift_option, Shift.call_flag');
        $this->db->from('tbl_staff as Staff');
        $this->db->join('(SELECT * FROM tbl_shift WHERE shift_date LIKE "'.$cond_date.'%") as Shift', 
                        'Staff.staff_id = Shift.staff_id', 
                        'left');
        $this->db->where('Staff.company_id', $this->user['company_id']);
        $this->db->order_by('Staff.staff_id', 'ASC');
        $this->db->order_by('Shift.shift_date', 'ASC');
        
        return $this->db->get()->result_array();
    }
    /**
     * Get shift record for a specific staff member and date
     */
    function getShiftByStaffAndDate($staff_id, $shift_date)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('shift_date', $shift_date);
        $query = $this->db->get();
        
        return $query->num_rows() > 0 ? $query->row_array() : null;
    }

    /**
     * Add a new shift record
     * 
     * @param array $shift_data Data to be inserted
     * @return int|bool The inserted ID on success, FALSE on failure
     */
    function addNewShift($shift_data)
    {
        // Ensure staff and company relationship is valid
        $this->db->select('company_id');
        $this->db->from('tbl_staff');
        $this->db->where('staff_id', $shift_data['staff_id']);
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false; // Staff not found
        }
        
        $staff = $query->row_array();
        
        // Only allow adding shifts for staff in user's company
        if ($staff['company_id'] != $this->user['company_id']) {
            return false;
        }
        
        // Insert the new shift record
        $this->db->insert($this->table, $shift_data);
        
        // Return the inserted ID if successful, FALSE otherwise
        return $this->db->affected_rows() > 0 ? $this->db->insert_id() : false;
    }

    /**
     * Update an existing shift record
     * 
     * @param int $id ID of the record to update
     * @param array $data Data to update
     * @return bool TRUE on success, FALSE on failure
     */
    function updateShift($id, $data)
    {
        // Verify the shift exists and belongs to user's company
        $this->db->select('s.staff_id, st.company_id');
        $this->db->from($this->table . ' as s');
        $this->db->join('tbl_staff as st', 's.staff_id = st.staff_id');
        $this->db->where('s.' . $this->primary_key, $id);
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false; // Shift not found
        }
        
        $shift = $query->row_array();
        
        // Only allow updating shifts for staff in user's company
        if ($shift['company_id'] != $this->user['company_id']) {
            return false;
        }
        
        // Update the shift record
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table, $data);
        
        // Return TRUE if successful, FALSE otherwise
        return $this->db->affected_rows() > 0;
    }
    
    /**
     * Delete a shift record
     * 
     * @param int $id ID of the record to delete
     * @return bool TRUE on success, FALSE on failure
     */
    function deleteShift($id)
    {
        // Verify the shift exists and belongs to user's company
        $this->db->select('s.staff_id, st.company_id');
        $this->db->from($this->table . ' as s');
        $this->db->join('tbl_staff as st', 's.staff_id = st.staff_id');
        $this->db->where('s.' . $this->primary_key, $id);
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false; // Shift not found
        }
        
        $shift = $query->row_array();
        
        // Only allow deleting shifts for staff in user's company
        if ($shift['company_id'] != $this->user['company_id']) {
            return false;
        }
        
        // Delete the shift record
        $this->db->where($this->primary_key, $id);
        $this->db->delete($this->table);
        
        // Return TRUE if successful, FALSE otherwise
        return $this->db->affected_rows() > 0;
    }
}