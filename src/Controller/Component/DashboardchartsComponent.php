<?php	

//Note: All $this are converted to $this->Controller in this component. created on 14-07-2017 by Amol
//To access the properties of main controller used initialize function.

namespace app\Controller\Component;

	use Cake\Controller\Component;
	use Cake\ORM\TableRegistry;

	class DashboardchartsComponent extends Component {
	
		
		public $components= array('Session');
		public $controller = null;
		public $session = null;
		
		public function initialize(array $config): void{
			parent::initialize($config);
			$this->Controller = $this->_registry->getController();
			$this->Session = $this->getController()->getRequest()->getSession();
		}

		
		
		// custome function for line chart graph

		public function lineChartGraph($username,$type){
			
			//initialize model in component
			$Dmi_user_role = TableRegistry::getTableLocator()->get('DmiUserRoles');
			$Dmi_final_submit = TableRegistry::getTableLocator()->get('DmiFinalSubmits');
			$Dmi_allocation = TableRegistry::getTableLocator()->get('DmiAllocations');
			$Dmi_ho_allocation = TableRegistry::getTableLocator()->get('DmiHoAllocations');
			
		//	$Dmi_applicant_payment_detail = TableRegistry::getTableLocator()->get('DmiApplicantPaymentDetails'); //added on 31-08-2018 by Amol
			$Dmi_user = TableRegistry::getTableLocator()->get('DmiUsers'); //added on 31-08-2018 by Amol
			$Dmi_pao_detail = TableRegistry::getTableLocator()->get('DmiPaoDetails'); //added on 31-08-2018 by Amol
			
			
			


				$check_user_role = $Dmi_user_role->find('all',array('conditions'=>array('user_email_id'=>$username)))->first();	
				
				if (!empty($check_user_role))//this condition added on 30-03-2017 by Amol(if user roles empty, no dashboard graphs)
				{
					$user_role = $check_user_role;

					
					
					// find last 12 months name	
					$i=11;//changed from 12 to 11 on 04-08-2017 to show from current month
					while ($i>=0) //changed from 1 to 0 on 04-08-2017 to show from current month
					{
						$month_count = '-'.$i;
						$month_name[$i] = date("F", strtotime($month_count." months"));

						
					$i = $i-1;
					}
		
					$this->Controller->set('month_name',$month_name);

					// For Super Admin User show over all applications status				
					if($user_role['super_admin'] == 'yes')
					{

						$i=11;//changed from 12 to 11 on 04-08-2017 to show from current month
						while($i>=0)//changed from 1 to 0 on 04-08-2017 to show from current month
						{
							
							$month_count = '-'.$i;
							$search_month_date_from = date("Y-m-01", strtotime($month_count." months"));
							
							$search_month_date_to = date("Y-m-01", strtotime(($month_count+1)." months")); // + and - will be -,so decrement
							
							$split_date = explode('-',$search_month_date_from); 
							
							$search_year = $split_date[0];
							$search_month = $split_date[1];
							
							
							//for month wise line chart				
							$find_total_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id',
                                                                                                            'conditions'=>array('status'=>'pending',
                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                            'group'=>'customer_id'))->toArray();
																								
							$total_applications_allocated[$i] = count($find_total_applications);	
																	
							$find_total_approved = $Dmi_final_submit->find('all',array('fields'=>'customer_id',
                                                                                                            'conditions'=>array('status'=>'approved','current_level'=>'level_3',
                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                            'group'=>'customer_id'))->toArray();

							$total_applications_accepted[$i] = count($find_total_approved);																		
						
						
							$i=$i-1;																
						}			

													

						
					}
					else{
					// For other users show allocated applications status	
						
						
						$i=11;//changed from 12 to 11 on 04-08-2017 to show from current month
						while($i>=0)//changed from 1 to 0 on 04-08-2017 to show from current month
						{
							$month_count = '-'.$i;
							$search_month_date_from = date("Y-m-01", strtotime($month_count." months"));
							
							$search_month_date_to = date("Y-m-01", strtotime(($month_count+1)." months")); // + and - will be -,so decrement
							
							$split_date = explode('-',$search_month_date_from); 
							
							$search_year = $split_date[0];
							$search_month = $split_date[1];
							

							if($user_role['mo_smo_inspection'] == 'yes')
							{
                                                            $find_mo_allocated_applications = $Dmi_allocation->find('all',array('conditions'=>array('level_1'=>$username,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_mo_applications[$i] = count($find_mo_allocated_applications);
								
							}else{
								
                                                            $find_mo_allocated_applications = null;
                                                            $total_mo_applications[$i] = 0;
								
							}
								

							
							if($user_role['io_inspection'] == 'yes')
							{
                                                            $find_io_allocated_applications = $Dmi_allocation->find('all',array('conditions'=>array('level_2'=>$username,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_io_applications[$i] = count($find_io_allocated_applications);
								
							}else{
                                                            $find_io_allocated_applications = null;
                                                            $total_io_applications[$i] = 0;
	
							}



							if($user_role['ro_inspection'] == 'yes')
							{
                                                            $find_ro_allocated_applications = $Dmi_allocation->find('all',array('conditions'=>array('level_3'=>$username,
                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_ro_applications[$i] = count($find_ro_allocated_applications);
								
							}else{
                                                            $find_ro_allocated_applications = null;
                                                            $total_ro_applications[$i] = 0;
	
							}
							
							
							
					//added new logic to show application status to PAO user
							$pao_application_ids = array();
							if($user_role['pao'] == 'yes')								
							{
                                                            //get user id from table
                                                            $user_details = $Dmi_user->find('all',array('conditions'=>array('email'=>$username)))->first();
                                                            $user_id = $user_details['id'];

                                                            //get pao id from pao table for this user id
                                                            $pao_details = $Dmi_pao_detail->find('all',array('conditions'=>array('pao_user_id'=>$user_id)))->first();
                                                            $pao_id = $pao_details['id'];

                                                            $find_pao_allocated_applications = $Dmi_applicant_payment_detail->find('all',array('conditions'=>array('pao_id'=>$username,'payment_confirmation'=>'pending',
                                                                                                                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_pao_applications[$i] = count($find_pao_allocated_applications);
							
							}else{
								
                                                            $find_pao_allocated_applications = null;
                                                            $total_pao_applications[$i] = 0;
								
							}
					//till here
							
							
							if($user_role['dy_ama'] == 'yes')
															
											 
							{
                                                            $find_dyama_allocated_applications = $Dmi_ho_allocation->find('all',array('conditions'=>array('dy_ama'=>$username,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_dyama_applications[$i] = count($find_dyama_allocated_applications);
	
							}else{

                                                            $find_dyama_allocated_applications = null;
                                                            $total_dyama_applications[$i] = 0;
	
							}
								
								
								
							if($user_role['ho_mo_smo'] == 'yes')
							{
                                                            $find_ho_mo_allocated_applications = $Dmi_ho_allocation->find('all',array('conditions'=>array('ho_mo_smo'=>$username,
                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_ho_mo_applications[$i] = count($find_ho_mo_allocated_applications);
								
							}else{

                                                            $find_ho_mo_allocated_applications = null;
                                                            $total_ho_mo_applications[$i] = 0;
	
							}
							
							
							
						if($user_role['jt_ama'] == 'yes')
							{
                                                            $find_jtama_allocated_applications = $Dmi_ho_allocation->find('all',array('conditions'=>array('jt_ama'=>$username,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_jtama_applications[$i] = count($find_jtama_allocated_applications);
								
							}else{
                                                            $find_jtama_allocated_applications = null;
                                                            $total_jtama_applications[$i] = 0;
	
							}
							
							
							if($user_role['ama'] == 'yes')
							{
                                                            $find_ama_allocated_applications = $Dmi_ho_allocation->find('all',array('conditions'=>array('ama'=>$username,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to)))->toArray();

                                                            $total_ama_applications[$i] = count($find_ama_allocated_applications);
								
							}else{				
                                                            $find_ama_allocated_applications = null;
                                                            $total_ama_applications[$i] = 0;
	
							}
							
				
							$total_applications_allocated[$i] = $total_mo_applications[$i] + $total_io_applications[$i] + $total_ro_applications[$i] + $total_dyama_applications[$i] + $total_pao_applications[$i] + $total_ho_mo_applications[$i] + $total_ama_applications[$i];
							
							$i = $i-1;
						}
						
						


					
				//calculation for other user accepted applications		
				
				
				
						$i=11;//changed from 12 to 11 on 04-08-2017 to show from current month
						while($i>=0)//changed from 1 to 0 on 04-08-2017 to show from current month
						{
							$month_count = '-'.$i;
							$search_month_date_from = date("Y-m-01", strtotime($month_count." months"));
							
							$search_month_date_to = date("Y-m-01", strtotime(($month_count+1)." months")); // + and - will be -,so decrement
							
							$split_date = explode('-',$search_month_date_from); 
							
							$search_year = $split_date[0];
							$search_month = $split_date[1];

							if($user_role['mo_smo_inspection'] == 'yes')
							{

                                                            $mo_allocated_applications = $Dmi_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('level_1'=>$username)))->toList();

                                                            if(!empty($mo_allocated_applications)){
                                                                $mo_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_1',
                                                                                                                        'customer_id IN'=>$mo_allocated_applications,
                                                                                                                        'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                        'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                $mo_accepted_applications = array();
                                                            }

                                                            $total_mo_accepted[$i] = count($mo_accepted_applications);
								
							}else{
								
                                                            $total_mo_accepted[$i] = 0;

							}
							
							
							
								

							if($user_role['io_inspection'] == 'yes')
							{
								
                                                            $io_allocated_applications = $Dmi_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('level_2'=>$username)))->toList();

                                                            if(!empty($io_allocated_applications)){
                                                                 $io_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_2',
                                                                                                                    'customer_id IN'=>$io_allocated_applications,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                    'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                
                                                                $io_accepted_applications = array();
                                                            }

                                                            $total_io_accepted[$i] = count($io_accepted_applications);
																												  
								
							}else{

                                                            $total_io_accepted[$i] = 0;
	
							}
							
					//till here
					

							if($user_role['ro_inspection'] == 'yes')
							{
								
                                                            $ro_allocated_applications = $Dmi_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('level_3'=>$username)))->toList();

                                                            if(!empty($ro_allocated_applications)){
                                                                $ro_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_3',
                                                                                                                            'customer_id IN'=>$ro_allocated_applications,
                                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                            'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                
                                                                $ro_accepted_applications = array();
                                                            }


                                                            $total_ro_accepted[$i] = count($ro_accepted_applications);
								
							}else{								
                                                            $total_ro_accepted[$i] = 0;								
							}
							
							
							
							if($user_role['dy_ama'] == 'yes')
							{
								
                                                            $dyama_allocated_applications = $Dmi_ho_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('dy_ama'=>$username)))->toList();								

                                                            if(!empty($dyama_allocated_applications)){
                                                                $dyama_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_3',
                                                                                                                    'customer_id IN'=>$dyama_allocated_applications,
                                                                                                                    'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                    'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                
                                                               $dyama_accepted_applications = array(); 
                                                            }

                                                            $total_dyama_accepted[$i] = count($dyama_accepted_applications);
								
							}else{								
                                                            $total_dyama_accepted[$i] = 0;

							}
	
							if($user_role['ho_mo_smo'] == 'yes')
							{
                                                            $ho_mo_allocated_applications = $Dmi_ho_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('ho_mo_smo'=>$username)))->toList();								

                                                            if(!empty($ho_mo_allocated_applications)){
                                                                $ho_mo_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_3',
                                                                                                                        'customer_id IN'=>$ho_mo_allocated_applications,
                                                                                                                        'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                        'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                
                                                                $ho_mo_accepted_applications = array();
                                                            }

                                                            $total_ho_mo_accepted[$i] = count($ho_mo_accepted_applications);
								
							}else{
								
                                                            $total_ho_mo_accepted[$i] = 0;

							}

							
							if($user_role['jt_ama'] == 'yes')
							{
								
                                                            $jtama_allocated_applications = $Dmi_ho_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('jt_ama'=>$username)))->toList();

                                                            if(!empty($jtama_allocated_applications)){
                                                                $jtama_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_3',
                                                                                                                                'customer_id IN'=>$jtama_allocated_applications,
                                                                                                                                'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                                'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                
                                                                $jtama_accepted_applications = array();
                                                            }
												
                                                            $total_jtama_accepted[$i] = count($jtama_accepted_applications);
								
							}else{
								
                                                            $total_jtama_accepted[$i] = 0;
	
							}
							

							if($user_role['ama'] == 'yes')
							{
								
                                                            $ama_allocated_applications = $Dmi_ho_allocation->find('list',array('valueField'=>'customer_id', 'conditions'=>array('ama'=>$username)))->toList();								

                                                            if(!empty($ama_allocated_applications)){
                                                                $ama_accepted_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id','conditions'=>array('status'=>'approved','current_level'=>'level_3',
                                                                                                                            'customer_id IN'=>$ama_allocated_applications,
                                                                                                                            'created >='=>$search_month_date_from, 'created <'=>$search_month_date_to),
                                                                                                                            'group'=>'customer_id'))->toArray();
                                                            }else{
                                                                
                                                                $ama_accepted_applications = array();
                                                            }

                                                            $total_ama_accepted[$i] = count($ama_accepted_applications);
								
							}else{
								
                                                            $total_ama_accepted[$i] = 0;
	
							}							
				
                                                    $total_applications_accepted[$i] = $total_mo_accepted[$i] + $total_io_accepted[$i] + $total_ro_accepted[$i] + $total_dyama_accepted[$i] + $total_ho_mo_accepted[$i] + $total_jtama_accepted[$i] + $total_ama_accepted[$i];

                                                    $i = $i-1;
						}

					}
					
					
					
					
					
					// setting variables array for above calculations for line chart
					// for total pending applications from last 12 months
					
					
					$i=11;//changed from 12 to 11 on 04-08-2017 to show from current month
					while($i>=0)//changed from 1 to 0 on 04-08-2017 to show from current month
					{								
                                            $month_allocated_data[$i] = $total_applications_allocated[$i];					

                                            $i=$i-1;
					}
					
					$this->Controller->set('month_allocated_data',$month_allocated_data);

							
					// for total approved applications from last 12 months							
					
					$i=11;//changed from 12 to 11 on 04-08-2017 to show from current month
					while($i>=0)//changed from 1 to 0 on 04-08-2017 to show from current month
					{								
                                            $month_approved_data[$i] = $total_applications_accepted[$i];					

                                            $i=$i-1;
					}
					
					$this->Controller->set('month_approved_data',$month_approved_data);
					
				
				}//end of first if condition on not empty check

		}
		
		

		
		// custome function for Pie chart data

			public function pieChartData($username){
								
				//initialize model in component
				$Dmi_user_role = TableRegistry::getTableLocator()->get('DmiUserRoles');
				$Dmi_final_submit = TableRegistry::getTableLocator()->get('DmiFinalSubmits');
				$Dmi_allocation = TableRegistry::getTableLocator()->get('DmiAllocations');
				$Dmi_ho_allocation = TableRegistry::getTableLocator()->get('DmiHoAllocations');
				$Dmi_user = TableRegistry::getTableLocator()->get('DmiUsers');//added on 22-03-2019 by Amol, it was not added when code upadsted.
				$Dmi_pao_detail = TableRegistry::getTableLocator()->get('DmiPaoDetails');//added on 22-03-2019 by Amol, it was not added when code upadsted.
				//$Dmi_applicant_payment_detail = TableRegistry::getTableLocator()->get('DmiApplicantPaymentDetails');//added on 22-03-2019 by Amol, it was not added when code upadsted.
				

					$site_inspection_count = 0;
					
					$ca_applications_count =0;
					$printing_applications_count = 0;
					$lab_applications_count = 0;
						

					$check_user_role = $Dmi_user_role->find('all',array('conditions'=>array('user_email_id'=>$username)))->first();	
					
					if(!empty($check_user_role))//this condition added on 30-03-2017 by Amol(if user roles empty, no dashboard graphs)
					{
						$user_role = $check_user_role;

						// For Super Admin User show over all applications status	
				
						if($user_role['super_admin'] == 'yes')
						{
					
							$find_total_site_inspected = $Dmi_final_submit->find('all',array('fields'=>'customer_id',
													'conditions'=>array('status'=>'approved','current_level'=>'level_2'),'group'=>'customer_id'))->toArray();	
																								
																								
							$site_inspection_count = count($find_total_site_inspected);

							$total_applications = $Dmi_final_submit->find('all',array('fields'=>'customer_id',
                                                                                                        'conditions'=>array('status'=>'pending'),
                                                                                                        'group'=>'customer_id'))->toArray();
																
							$total_allocated_applications = count($total_applications);																
																								

																											
							foreach($total_applications as $each_type)
							{
								if($each_type['customer_id']!=null){
									//for application type count
									$split_id = explode('/',$each_type['customer_id']);
									
									if($split_id[1] == 1)
									{									
										$ca_applications_count = $ca_applications_count+1;
										
									}
									elseif($split_id[1] == 2){									
										$printing_applications_count = $printing_applications_count+1;
										
									}
									elseif($split_id[1] == 3){									
										$lab_applications_count = $lab_applications_count+1;
										
									}
								}
								
							}

						}
						else{
						// for other users

								// MO allocated user
		
								if($user_role['mo_smo_inspection'] == 'yes')
								{
									$find_mo_allocated = $Dmi_allocation->find('all',array('conditions'=>array('level_1'=>$username)))->toArray();
									
			  
									foreach($find_mo_allocated as $each_id)
									{
										if($each_id['customer_id']!=null){
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}

									}
									
								}else{
									
									$find_mo_allocated = array();
								}
								

								// IO allocated user			
								if($user_role['io_inspection'] == 'yes')
								{
									$find_io_allocated = $Dmi_allocation->find('all',array('conditions'=>array('level_2'=>$username)))->toArray();
									
			  
									foreach($find_io_allocated as $each_id)
									{
										if($each_id['customer_id']!=null){
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																															'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											
											

											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}
				 
									}
								
								}else{
									
									$find_io_allocated = array();
								}
								
								
								
					//added new logic to show application status to PAO user
						
								
								if($user_role['pao'] == 'yes')								
								{
									//get user id from table
									$user_details = $Dmi_user->find('all',array('conditions'=>array('email'=>$username)))->first();
									$user_id = $user_details['id'];
									
									//get pao id from pao table for this user id
									$pao_details = $Dmi_pao_detail->find('all',array('conditions'=>array('pao_user_id'=>$user_id)))->first();
									$pao_id = $pao_details['id'];
									
									$find_pao_allocated = $Dmi_applicant_payment_detail->find('all',array('conditions'=>array('pao_id'=>$username,
																											'payment_confirmation'=>'confirmed')))->toArray();
									

									foreach($find_pao_allocated as $each_id)
									{

										if($each_id['customer_id']!=null){
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}

									}
									
								}else{
									
									$find_pao_allocated = array();
								}
					//till here		
								

								
								
								// RO allocated user			
								if($user_role['ro_inspection'] == 'yes')
								{
									$find_ro_allocated = $Dmi_allocation->find('all',array('conditions'=>array('level_3'=>$username)))->toArray();
									
			  
									foreach($find_ro_allocated as $each_id)
									{
										if($each_id['customer_id']!=null){
										
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																															'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											
											
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}
									
									}
								
								}
								else{
									
									$find_ro_allocated = array();
								}
								
								
								
								
								
								
								
								// DY AMA allocated user
			
								if($user_role['dy_ama'] == 'yes')
								{
									$find_dyama_allocated = $Dmi_ho_allocation->find('all',array('conditions'=>array('dy_ama'=>$username)))->toArray();
									
			  
									foreach($find_dyama_allocated as $each_id)
									{
										
										if($each_id['customer_id']!=null){
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																															'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											
											
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}
								
									}
								
								}
								else{
									
									$find_dyama_allocated = array();
								}
								
								
								
								
								
								
								// HO MO allocated user
															 
										   
										   
												 
											
								if($user_role['ho_mo_smo'] == 'yes')
								{
									$find_ho_mo_allocated = $Dmi_ho_allocation->find('all',array('conditions'=>array('ho_mo_smo'=>$username)))->toArray();
									
			  
									foreach($find_ho_mo_allocated as $each_id)
									{
										if($each_id['customer_id']!=null){
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																															'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											
											
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}
				 
									}
									
								}
								else{
									
									$find_ho_mo_allocated = array();
								}
								
								

								
								
								// JT AMA allocated user
										   
										   
												 
											
								if($user_role['jt_ama'] == 'yes')
								{
									$find_jtama_allocated = $Dmi_ho_allocation->find('all',array('conditions'=>array('jt_ama'=>$username)))->toArray();
									
			  
									foreach($find_jtama_allocated as $each_id)
									{
										if($each_id['customer_id']!=null){
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																															'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											
											
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}
				 
									}
								
								}
								else{
									
									$find_jtama_allocated = array();
								}
								

								
								
								// AMA allocated user
										   
										   
												 
											
								if($user_role['ama'] == 'yes')
								{
									$find_ama_allocated = $Dmi_ho_allocation->find('all',array('conditions'=>array('ama'=>$username)))->toArray();
									
			  
									foreach($find_ama_allocated as $each_id)
									{
										
										if($each_id['customer_id']!=null){
											//for site inspection count
											$find_approved = $Dmi_final_submit->find('all',array('conditions'=>array('customer_id'=>$each_id['customer_id'],
																															'status'=>'approved','current_level'=>'level_2')))->first();
																															
											if(!empty($find_approved))
											{
												$site_inspection_count = $site_inspection_count+1;
												
											}
											
											
											
											
		
											
											//for application type count
											$split_id = explode('/',$each_id['customer_id']);
											
											if($split_id[1] == 1)
											{
												
												$ca_applications_count = $ca_applications_count+1;
												
											}
											elseif($split_id[1] == 2){
												
												$printing_applications_count = $printing_applications_count+1;
												
											}
											elseif($split_id[1] == 3){
												
												$lab_applications_count = $lab_applications_count+1;
												
											}
										}
								
									}
									
								}
								else{
									
									$find_ama_allocated = array();
								}
								
				
						$total_allocated_applications = count($find_mo_allocated) +
														count($find_io_allocated) +
																			 
																																		  
																													  
			   
																	   
														count($find_ro_allocated) +
														count($find_dyama_allocated) +
														count($find_pao_allocated)+
			   
																			 
														count($find_ho_mo_allocated) +
														count($find_jtama_allocated) +
														count($find_ama_allocated);
								
																		
																																			  
																													  
										   
				
							
						}
				
				
						
				
		
						
						// calculate the values at last
							
							if($total_allocated_applications != 0)
							{
								$siteinspection_percentage = ($site_inspection_count*100)/$total_allocated_applications;
								$ca_percentage = ($ca_applications_count*100)/$total_allocated_applications;
								$printing_percentage = ($printing_applications_count*100)/$total_allocated_applications;
								$lab_percentage = ($lab_applications_count*100)/$total_allocated_applications;
								
							}
							else{
								
								$siteinspection_percentage =0;
								$ca_percentage =0;
								$printing_percentage =0;
								$lab_percentage =0;
								
							}
							
							
							
							
							$this->Controller->set('siteinspection_percentage',$siteinspection_percentage);
							$this->Controller->set('ca_percentage',$ca_percentage);
							$this->Controller->set('printing_percentage',$printing_percentage);
							$this->Controller->set('lab_percentage',$lab_percentage);
							
							
							
							
							$this->Controller->set('site_inspection_count',$site_inspection_count);
							$this->Controller->set('ca_applications_count',$ca_applications_count);
							$this->Controller->set('printing_applications_count',$printing_applications_count);
							$this->Controller->set('lab_applications_count',$lab_applications_count);
							
							$this->Controller->set('total_allocated_applications',$total_allocated_applications);
							
						
				
				
					}//end of first if condition on not empty check
				
			}
		
		
			
		
	}

	
?>