<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class PN_FINANCE_MANAGER_Post_Type_Liability {
  public function pn_finance_manager_liability_get_fields($liability_id = 0) {
    $fields = [];
    $fields['pn_finance_manager_liability_title'] = [
      'id' => 'pn_finance_manager_liability_title',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'required' => false,
      'value' => !empty($liability_id) ? esc_html(get_the_title($liability_id)) : '',
      'label' => __('Liability title', 'pn-finance-manager'),
      'placeholder' => __('Liability title', 'pn-finance-manager'),
    ];
    $fields['pn_finance_manager_liability_description'] = [
      'id' => 'pn_finance_manager_liability_description',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'textarea',
      'required' => false,
      'value' => !empty($liability_id) ? (str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post($liability_id)->post_content))) : '',
      'label' => __('Comments', 'pn-finance-manager'),
      'placeholder' => __('Optional comments', 'pn-finance-manager'),
    ];
    return $fields;
  }

  public function pn_finance_manager_liability_get_fields_meta() {
    $fields_meta = [];
    $fields_meta['pn_finance_manager_liability_date'] = [
      'id' => 'pn_finance_manager_liability_date',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'date',
      'label' => __('Liability date', 'pn-finance-manager'),
      'placeholder' => __('Liability date', 'pn-finance-manager'),
    ];
    $fields_meta['pn_finance_manager_liability_time'] = [
      'id' => 'pn_finance_manager_liability_time',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'time',
      'label' => __('Liability time', 'pn-finance-manager'),
      'placeholder' => __('Liability time', 'pn-finance-manager'),
    ];

    $fields_meta['pn_finance_manager_liability_type'] = [
      'id' => 'pn_finance_manager_liability_type',
      'class' => 'pn-finance-manager-select pn-finance-manager-width-100-percent',
      'input' => 'select',
      'parent' => 'this',
      'options' => PN_FINANCE_MANAGER_Data::pn_finance_manager_get_liability_types(),
      'label' => __('Liability type', 'pn-finance-manager'),
      'placeholder' => __('Select liability type', 'pn-finance-manager'),
    ];

    // Mortgage sub-fields
    $fields_meta['pn_finance_manager_mortgage_original_amount'] = [
      'id' => 'pn_finance_manager_mortgage_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'mortgage',
    ];
    $fields_meta['pn_finance_manager_mortgage_remaining_balance'] = [
      'id' => 'pn_finance_manager_mortgage_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'mortgage',
    ];
    $fields_meta['pn_finance_manager_mortgage_interest_rate'] = [
      'id' => 'pn_finance_manager_mortgage_interest_rate',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'max' => '100',
      'label' => __('Interest Rate (%)', 'pn-finance-manager'),
      'placeholder' => __('Enter interest rate', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'mortgage',
    ];
    $fields_meta['pn_finance_manager_mortgage_monthly_payment'] = [
      'id' => 'pn_finance_manager_mortgage_monthly_payment',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Monthly Payment', 'pn-finance-manager'),
      'placeholder' => __('Enter monthly payment', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'mortgage',
    ];
    $fields_meta['pn_finance_manager_mortgage_maturity_date'] = [
      'id' => 'pn_finance_manager_mortgage_maturity_date',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'date',
      'label' => __('Maturity Date', 'pn-finance-manager'),
      'placeholder' => __('Select maturity date', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'mortgage',
    ];

    // Car Loan sub-fields
    $fields_meta['pn_finance_manager_car_loan_original_amount'] = [
      'id' => 'pn_finance_manager_car_loan_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'car_loan',
    ];
    $fields_meta['pn_finance_manager_car_loan_remaining_balance'] = [
      'id' => 'pn_finance_manager_car_loan_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'car_loan',
    ];
    $fields_meta['pn_finance_manager_car_loan_interest_rate'] = [
      'id' => 'pn_finance_manager_car_loan_interest_rate',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'max' => '100',
      'label' => __('Interest Rate (%)', 'pn-finance-manager'),
      'placeholder' => __('Enter interest rate', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'car_loan',
    ];
    $fields_meta['pn_finance_manager_car_loan_monthly_payment'] = [
      'id' => 'pn_finance_manager_car_loan_monthly_payment',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Monthly Payment', 'pn-finance-manager'),
      'placeholder' => __('Enter monthly payment', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'car_loan',
    ];

    // Student Loan sub-fields
    $fields_meta['pn_finance_manager_student_loan_original_amount'] = [
      'id' => 'pn_finance_manager_student_loan_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'student_loan',
    ];
    $fields_meta['pn_finance_manager_student_loan_remaining_balance'] = [
      'id' => 'pn_finance_manager_student_loan_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'student_loan',
    ];
    $fields_meta['pn_finance_manager_student_loan_interest_rate'] = [
      'id' => 'pn_finance_manager_student_loan_interest_rate',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'max' => '100',
      'label' => __('Interest Rate (%)', 'pn-finance-manager'),
      'placeholder' => __('Enter interest rate', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'student_loan',
    ];
    $fields_meta['pn_finance_manager_student_loan_monthly_payment'] = [
      'id' => 'pn_finance_manager_student_loan_monthly_payment',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Monthly Payment', 'pn-finance-manager'),
      'placeholder' => __('Enter monthly payment', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'student_loan',
    ];

    // Credit Card sub-fields
    $fields_meta['pn_finance_manager_credit_card_limit'] = [
      'id' => 'pn_finance_manager_credit_card_limit',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Credit Limit', 'pn-finance-manager'),
      'placeholder' => __('Enter credit limit', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'credit_card',
    ];
    $fields_meta['pn_finance_manager_credit_card_balance'] = [
      'id' => 'pn_finance_manager_credit_card_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Current Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter current balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'credit_card',
    ];
    $fields_meta['pn_finance_manager_credit_card_interest_rate'] = [
      'id' => 'pn_finance_manager_credit_card_interest_rate',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'max' => '100',
      'label' => __('Interest Rate (%)', 'pn-finance-manager'),
      'placeholder' => __('Enter interest rate', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'credit_card',
    ];
    $fields_meta['pn_finance_manager_credit_card_minimum_payment'] = [
      'id' => 'pn_finance_manager_credit_card_minimum_payment',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Minimum Payment', 'pn-finance-manager'),
      'placeholder' => __('Enter minimum payment', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'credit_card',
    ];

    // Personal Loan sub-fields
    $fields_meta['pn_finance_manager_personal_loan_original_amount'] = [
      'id' => 'pn_finance_manager_personal_loan_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'personal_loan',
    ];
    $fields_meta['pn_finance_manager_personal_loan_remaining_balance'] = [
      'id' => 'pn_finance_manager_personal_loan_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'personal_loan',
    ];
    $fields_meta['pn_finance_manager_personal_loan_interest_rate'] = [
      'id' => 'pn_finance_manager_personal_loan_interest_rate',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'max' => '100',
      'label' => __('Interest Rate (%)', 'pn-finance-manager'),
      'placeholder' => __('Enter interest rate', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'personal_loan',
    ];
    $fields_meta['pn_finance_manager_personal_loan_monthly_payment'] = [
      'id' => 'pn_finance_manager_personal_loan_monthly_payment',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Monthly Payment', 'pn-finance-manager'),
      'placeholder' => __('Enter monthly payment', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'personal_loan',
    ];

    // Medical Debt sub-fields
    $fields_meta['pn_finance_manager_medical_debt_original_amount'] = [
      'id' => 'pn_finance_manager_medical_debt_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'medical_debt',
    ];
    $fields_meta['pn_finance_manager_medical_debt_remaining_balance'] = [
      'id' => 'pn_finance_manager_medical_debt_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'medical_debt',
    ];
    $fields_meta['pn_finance_manager_medical_debt_provider'] = [
      'id' => 'pn_finance_manager_medical_debt_provider',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Provider', 'pn-finance-manager'),
      'placeholder' => __('Enter provider name', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'medical_debt',
    ];

    // Business Loan sub-fields
    $fields_meta['pn_finance_manager_business_loan_original_amount'] = [
      'id' => 'pn_finance_manager_business_loan_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'business_loan',
    ];
    $fields_meta['pn_finance_manager_business_loan_remaining_balance'] = [
      'id' => 'pn_finance_manager_business_loan_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'business_loan',
    ];
    $fields_meta['pn_finance_manager_business_loan_interest_rate'] = [
      'id' => 'pn_finance_manager_business_loan_interest_rate',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'max' => '100',
      'label' => __('Interest Rate (%)', 'pn-finance-manager'),
      'placeholder' => __('Enter interest rate', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'business_loan',
    ];
    $fields_meta['pn_finance_manager_business_loan_monthly_payment'] = [
      'id' => 'pn_finance_manager_business_loan_monthly_payment',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Monthly Payment', 'pn-finance-manager'),
      'placeholder' => __('Enter monthly payment', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'business_loan',
    ];

    // Tax Debt sub-fields
    $fields_meta['pn_finance_manager_tax_debt_year'] = [
      'id' => 'pn_finance_manager_tax_debt_year',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'min' => '1900',
      'max' => '2100',
      'label' => __('Tax Year', 'pn-finance-manager'),
      'placeholder' => __('Enter tax year', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'tax_debt',
    ];
    $fields_meta['pn_finance_manager_tax_debt_original_amount'] = [
      'id' => 'pn_finance_manager_tax_debt_original_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Original Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter original amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'tax_debt',
    ];
    $fields_meta['pn_finance_manager_tax_debt_remaining_balance'] = [
      'id' => 'pn_finance_manager_tax_debt_remaining_balance',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Remaining Balance', 'pn-finance-manager'),
      'placeholder' => __('Enter remaining balance', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'tax_debt',
    ];

    // Other sub-fields
    $fields_meta['pn_finance_manager_other_liability_amount'] = [
      'id' => 'pn_finance_manager_other_liability_amount',
      'class' => 'pn-finance-manager-input pn-finance-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'step' => '0.01',
      'min' => '0',
      'label' => __('Amount', 'pn-finance-manager'),
      'placeholder' => __('Enter amount', 'pn-finance-manager'),
      'parent' => 'pn_finance_manager_liability_type',
      'parent_option' => 'other',
    ];

    $fields_meta['pn_finance_manager_ajax_nonce'] = [
      'id' => 'pn_finance_manager_ajax_nonce',
      'input' => 'input',
      'type' => 'nonce',
    ];
    return $fields_meta;
  }

  public function pn_finance_manager_liability_get_fields_meta_with_values($liability_id = 0) {
    $fields_meta = self::pn_finance_manager_liability_get_fields_meta();

    if (!empty($liability_id)) {
      $value_fields = [
        'pn_finance_manager_liability_date',
        'pn_finance_manager_liability_time',
        'pn_finance_manager_liability_type',
        // Mortgage
        'pn_finance_manager_mortgage_original_amount',
        'pn_finance_manager_mortgage_remaining_balance',
        'pn_finance_manager_mortgage_interest_rate',
        'pn_finance_manager_mortgage_monthly_payment',
        'pn_finance_manager_mortgage_maturity_date',
        // Car Loan
        'pn_finance_manager_car_loan_original_amount',
        'pn_finance_manager_car_loan_remaining_balance',
        'pn_finance_manager_car_loan_interest_rate',
        'pn_finance_manager_car_loan_monthly_payment',
        // Student Loan
        'pn_finance_manager_student_loan_original_amount',
        'pn_finance_manager_student_loan_remaining_balance',
        'pn_finance_manager_student_loan_interest_rate',
        'pn_finance_manager_student_loan_monthly_payment',
        // Credit Card
        'pn_finance_manager_credit_card_limit',
        'pn_finance_manager_credit_card_balance',
        'pn_finance_manager_credit_card_interest_rate',
        'pn_finance_manager_credit_card_minimum_payment',
        // Personal Loan
        'pn_finance_manager_personal_loan_original_amount',
        'pn_finance_manager_personal_loan_remaining_balance',
        'pn_finance_manager_personal_loan_interest_rate',
        'pn_finance_manager_personal_loan_monthly_payment',
        // Medical Debt
        'pn_finance_manager_medical_debt_original_amount',
        'pn_finance_manager_medical_debt_remaining_balance',
        'pn_finance_manager_medical_debt_provider',
        // Business Loan
        'pn_finance_manager_business_loan_original_amount',
        'pn_finance_manager_business_loan_remaining_balance',
        'pn_finance_manager_business_loan_interest_rate',
        'pn_finance_manager_business_loan_monthly_payment',
        // Tax Debt
        'pn_finance_manager_tax_debt_year',
        'pn_finance_manager_tax_debt_original_amount',
        'pn_finance_manager_tax_debt_remaining_balance',
        // Other
        'pn_finance_manager_other_liability_amount',
      ];

      foreach ($value_fields as $field_key) {
        if (isset($fields_meta[$field_key])) {
          $fields_meta[$field_key]['value'] = get_post_meta($liability_id, $field_key, true);
        }
      }
    }

    return $fields_meta;
  }

  public function pn_finance_manager_liability_register_post_type() {
    $labels = [
      'name'                => _x('Liabilities', 'Post Type general name', 'pn-finance-manager'),
      'singular_name'       => _x('Liability', 'Post Type singular name', 'pn-finance-manager'),
      'menu_name'           => esc_html(__('Liabilities', 'pn-finance-manager')),
      'parent_item_colon'   => esc_html(__('Parent Liability', 'pn-finance-manager')),
      'all_items'           => esc_html(__('All Liabilities', 'pn-finance-manager')),
      'view_item'           => esc_html(__('View Liability', 'pn-finance-manager')),
      'add_new_item'        => esc_html(__('Add new Liability', 'pn-finance-manager')),
      'add_new'             => esc_html(__('Add new Liability', 'pn-finance-manager')),
      'edit_item'           => esc_html(__('Edit Liability', 'pn-finance-manager')),
      'update_item'         => esc_html(__('Update Liability', 'pn-finance-manager')),
      'search_items'        => esc_html(__('Search Liabilities', 'pn-finance-manager')),
      'not_found'           => esc_html(__('No Liability found', 'pn-finance-manager')),
      'not_found_in_trash'  => esc_html(__('No Liability found in Trash', 'pn-finance-manager')),
    ];
    $args = [
      'labels'              => $labels,
      'rewrite'             => ['slug' => (!empty(get_option('pn_finance_manager_liability_slug')) ? get_option('pn_finance_manager_liability_slug') : 'liability'), 'with_front' => false],
      'label'               => esc_html(__('Liabilities', 'pn-finance-manager')),
      'description'         => esc_html(__('Liability description', 'pn-finance-manager')),
      'supports'            => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'page-attributes', ],
      'hierarchical'        => true,
      'public'              => false,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'menu_position'       => 6,
      'menu_icon'           => esc_url(PN_FINANCE_MANAGER_URL . 'assets/media/pn-finance-manager-liability-menu-icon.svg'),
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => false,
      'capability_type'     => 'page',
      'taxonomies'          => ['pnfm_liability_category'],
      'show_in_rest'        => true, /* REST API enabled for Gutenberg */
    ];
    register_post_type('pnfm_liability', $args);
    add_theme_support('post-thumbnails', ['page', 'pnfm_liability']);
    add_filter('rest_pre_dispatch', [$this, 'pn_finance_manager_block_liability_rest_api'], 10, 3);
  }

  public function pn_finance_manager_block_liability_rest_api($result, $server, $request) {
    $route = $request->get_route();
    
    // Solo bloquear acceso público a la REST API, no el acceso del admin
    if (strpos($route, '/wp/v2/pn_finance_manager_liability') !== false) {
      // Permitir acceso si el usuario está autenticado y tiene permisos de administrador
      if (is_user_logged_in() && current_user_can('edit_posts')) {
        return $result;
      }
      
      // Bloquear acceso público
      return new WP_Error(
        'rest_forbidden',
        __('Sorry, you are not allowed to access this resource.', 'pn-finance-manager'),
        ['status' => 403]
      );
    }
    
    return $result;
  }

  public function pn_finance_manager_liability_add_meta_box() {
    add_meta_box('pn_finance_manager_liability_meta_box', esc_html(__('Liability details', 'pn-finance-manager')), [$this, 'pn_finance_manager_liability_meta_box_function'], 'pnfm_liability', 'normal', 'high', ['__block_editor_compatible_meta_box' => true,]);
  }

  public function pn_finance_manager_liability_meta_box_function($post) {
    foreach (self::pn_finance_manager_liability_get_fields_meta() as $field) {
      if (!is_null(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($field, 'post', $post->ID))) {
        echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($field, 'post', $post->ID), PN_FINANCE_MANAGER_KSES);
      }
    }
  }

  public function pn_finance_manager_liability_single_template($single) {
    if (get_post_type() == 'pnfm_liability') {
      if (file_exists(PN_FINANCE_MANAGER_DIR . 'templates/public/single-pnfm_liability.php')) {
        return PN_FINANCE_MANAGER_DIR . 'templates/public/single-pnfm_liability.php';
      }
    }
    return $single;
  }

  public function pn_finance_manager_liability_archive_template($archive) {
    if (get_post_type() == 'pnfm_liability') {
      if (file_exists(PN_FINANCE_MANAGER_DIR . 'templates/public/archive-pnfm_liability.php')) {
        return PN_FINANCE_MANAGER_DIR . 'templates/public/archive-pnfm_liability.php';
      }
    }
    return $archive;
  }

  public function pn_finance_manager_liability_save_post($post_id, $cpt, $update) {
    if($cpt->post_type == 'pnfm_liability' && array_key_exists('pn_finance_manager_liability_form', $_POST)){
      if (!array_key_exists('pn_finance_manager_ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'pn_finance_manager_nonce_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'pn-finance-manager')),
        ]);
        exit;
      }
      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_finance_manager_ajax_nonce'])), 'pn-finance-manager-nonce')) {
        echo wp_json_encode([
          'error_key' => 'pn_finance_manager_nonce_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'pn-finance-manager')),
        ]);
        exit;
      }
      if (!array_key_exists('pn_finance_manager_duplicate', $_POST)) {
        foreach (array_merge(self::pn_finance_manager_liability_get_fields(), self::pn_finance_manager_liability_get_fields_meta()) as $field) {
          $input = array_key_exists('input', $field) ? $field['input'] : '';
          if (array_key_exists($field['id'], $_POST) || $input == 'html_multi') {
            $value = array_key_exists($field['id'], $_POST) ? 
              PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(
                wp_unslash($_POST[$field['id']]),
                $field['input'], 
                !empty($field['type']) ? $field['type'] : '',
                $field
              ) : '';
            if (!empty($input)) {
              switch ($input) {
                case 'input':
                  if (array_key_exists('type', $field) && $field['type'] == 'checkbox') {
                    if (isset($_POST[$field['id']])) {
                      update_post_meta($post_id, $field['id'], $value);
                    } else {
                      update_post_meta($post_id, $field['id'], '');
                    }
                  } else {
                    update_post_meta($post_id, $field['id'], $value);
                  }
                  break;
                case 'select':
                  if (array_key_exists('multiple', $field) && $field['multiple']) {
                    $multi_array = [];
                    $empty = true;
                    foreach (wp_unslash($_POST[$field['id']]) as $multi_value) {
                      $multi_array[] = PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(
                        $multi_value, 
                        $field['input'], 
                        !empty($field['type']) ? $field['type'] : '',
                        $field
                      );
                    }
                    update_post_meta($post_id, $field['id'], $multi_array);
                  } else {
                    update_post_meta($post_id, $field['id'], $value);
                  }
                  break;
                case 'html_multi':
                  foreach ($field['html_multi_fields'] as $multi_field) {
                    if (array_key_exists($multi_field['id'], $_POST)) {
                      $multi_array = [];
                      $empty = true;
                      $sanitized_post_data = isset($_POST[$multi_field['id']]) ? 
                        array_map(function($v) { return sanitize_text_field(wp_unslash($v)); }, (array)$_POST[$multi_field['id']]) : [];
                      foreach ($sanitized_post_data as $multi_value) {
                        if (!empty($multi_value)) {
                          $empty = false;
                        }
                        $multi_array[] = PN_FINANCE_MANAGER_Forms::pn_finance_manager_sanitizer(
                          $multi_value, 
                          $multi_field['input'], 
                          !empty($multi_field['type']) ? $multi_field['type'] : '',
                          $multi_field
                        );
                      }
                      if (!$empty) {
                        update_post_meta($post_id, $multi_field['id'], $multi_array);
                      } else {
                        update_post_meta($post_id, $multi_field['id'], '');
                      }
                    }
                  }
                  break;
                default:
                  update_post_meta($post_id, $field['id'], $value);
                  break;
              }
            }
          } else {
            update_post_meta($post_id, $field['id'], '');
          }
        }
      }
    }
  }

  public function pn_finance_manager_liability_form_save($element_id, $key_value, $form_type, $form_subtype) {
    $post_type = !empty(get_post_type($element_id)) ? get_post_type($element_id) : 'pnfm_liability';
    if ($post_type == 'pnfm_liability') {
      switch ($form_type) {
        case 'post':
          switch ($form_subtype) {
            case 'post_new':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'pn_finance_manager_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($element_id, $key);
                  }
                }
              }

              if (empty($pn_finance_manager_liability_title)) {
                $liability_types = PN_FINANCE_MANAGER_Data::pn_finance_manager_get_liability_types();
                $type_key = isset($pn_finance_manager_key_value['pn_finance_manager_liability_type']) ? $pn_finance_manager_key_value['pn_finance_manager_liability_type'] : 'other';
                $type_label = isset($liability_types[$type_key]) ? $liability_types[$type_key] : __('Liability', 'pn-finance-manager');
                $pn_finance_manager_liability_title = $type_label . ' - ' . gmdate('Y-m-d H:i');
              }

              $post_functions = new PN_FINANCE_MANAGER_Functions_Post();
              $liability_id = $post_functions->pn_finance_manager_insert_post(esc_html($pn_finance_manager_liability_title), $pn_finance_manager_liability_description, '', sanitize_title(esc_html($pn_finance_manager_liability_title)), 'pnfm_liability', 'publish', get_current_user_id());
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  update_post_meta($liability_id, $key, $value);
                }
              }
              break;
            case 'post_edit':
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  if (strpos($key, 'pn_finance_manager_') !== false) {
                    ${$key} = $value;
                    delete_post_meta($element_id, $key);
                  }
                }
              }
              $liability_id = $element_id;
              wp_update_post(['ID' => $liability_id, 'post_title' => $pn_finance_manager_liability_title, 'post_content' => $pn_finance_manager_liability_description,]);
              if (!empty($key_value)) {
                foreach ($key_value as $key => $value) {
                  update_post_meta($liability_id, $key, $value);
                }
              }
              break;
          }
      }
    }
  }

  public function pn_finance_manager_liability_register_scripts() {
    if (!wp_script_is('pn-finance-manager-aux', 'registered')) {
      wp_register_script('pn-finance-manager-aux', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-aux.js', [], PN_FINANCE_MANAGER_VERSION, true);
    }
    if (!wp_script_is('pn-finance-manager-forms', 'registered')) {
      wp_register_script('pn-finance-manager-forms', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-forms.js', [], PN_FINANCE_MANAGER_VERSION, true);
    }
    if (!wp_script_is('pn-finance-manager-selector', 'registered')) {
      wp_register_script('pn-finance-manager-selector', PN_FINANCE_MANAGER_URL . 'assets/js/pn-finance-manager-selector.js', [], PN_FINANCE_MANAGER_VERSION, true);
    }
  }
  public function pn_finance_manager_liability_print_scripts() {
    wp_print_scripts(['pn-finance-manager-aux', 'pn-finance-manager-forms', 'pn-finance-manager-selector']);
  }

  public function pn_finance_manager_liability_list_wrapper() {
    $liability_types = PN_FINANCE_MANAGER_Data::pn_finance_manager_get_liability_types();

    // Get existing liability types to limit filter options
    $liability_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'pnfm_liability',
      'post_status' => 'any',
    ];
    if (class_exists('Polylang')) {
      $liability_atts['lang'] = pll_current_language('slug');
    }
    $all_liability_ids = get_posts($liability_atts);
    $all_liability_ids = PN_FINANCE_MANAGER_Functions_User::pn_finance_manager_filter_user_posts($all_liability_ids, 'pnfm_liability');
    $existing_types = [];
    foreach ($all_liability_ids as $lid) {
      $t = get_post_meta($lid, 'pn_finance_manager_liability_type', true);
      if (!empty($t)) {
        $existing_types[$t] = true;
      }
    }

    ob_start();
    ?>
      <div class="pn-finance-manager-pn_finance_manager_liability-list pn-finance-manager-mb-50">
        <div class="pn-finance-manager-cpt-search-container pn-finance-manager-mb-20 pn-finance-manager-text-align-right">
          <div class="pn-finance-manager-cpt-search-wrapper pn-finance-manager-pnfm_liability-search-wrapper">
            <input type="text" class="pn-finance-manager-cpt-search-input pn-finance-manager-pnfm_liability-search-input pn-finance-manager-input pn-finance-manager-display-none" placeholder="<?php esc_attr_e('Filter...', 'pn-finance-manager'); ?>" />
            <i class="material-icons-outlined pn-finance-manager-cpt-search-toggle pn-finance-manager-pnfm_liability-search-toggle pn-finance-manager-cursor-pointer pn-finance-manager-font-size-30 pn-finance-manager-vertical-align-middle pn-finance-manager-tooltip" title="<?php esc_attr_e('Search Liabilities', 'pn-finance-manager'); ?>">search</i>
            <i class="material-icons-outlined pn-finance-manager-cpt-filter-toggle pn-finance-manager-pnfm_liability-filter-toggle pn-finance-manager-cursor-pointer pn-finance-manager-font-size-30 pn-finance-manager-vertical-align-middle pn-finance-manager-tooltip" title="<?php esc_attr_e('Filter by type', 'pn-finance-manager'); ?>">filter_list</i>
            <?php if (is_user_logged_in()) : ?>
            <a href="#" class="pn-finance-manager-popup-open-ajax pn-finance-manager-text-decoration-none" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-add" data-pn-finance-manager-ajax-type="pn_finance_manager_liability_new">
              <i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-font-size-30 pn-finance-manager-vertical-align-middle pn-finance-manager-tooltip" title="<?php esc_attr_e('Add new Liability', 'pn-finance-manager'); ?>">add</i>
            </a>
            <?php endif; ?>
          </div>
          <div class="pn-finance-manager-cpt-filter-dropdown pn-finance-manager-pnfm_liability-filter-dropdown pn-finance-manager-display-none">
            <select class="pn-finance-manager-cpt-filter-select pn-finance-manager-pnfm_liability-filter-select pn-finance-manager-select" data-pn-finance-manager-type-meta="pn_finance_manager_liability_type">
              <option value=""><?php esc_html_e('All types', 'pn-finance-manager'); ?></option>
              <?php foreach ($liability_types as $type_key => $type_label): ?>
                <?php if (isset($existing_types[$type_key])): ?>
                <option value="<?php echo esc_attr($type_key); ?>"><?php echo esc_html($type_label); ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="pn-finance-manager-pn_finance_manager_liability-list-wrapper pn-finance-manager-pnfm_liability-list">
          <?php echo wp_kses(self::pn_finance_manager_liability_list(), PN_FINANCE_MANAGER_KSES); ?>
        </div>
      </div>

      <div class="pn-finance-manager-pnfm_liability-statistics">
        <?php echo wp_kses_post(self::pn_finance_manager_liability_statistics()); ?>
      </div>
    <?php
    $return_string = ob_get_contents();
    ob_end_clean();
    return $return_string;
  }

  public static function pn_finance_manager_liability_statistics() {
    $liability_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'pnfm_liability',
      'post_status' => 'any',
      'orderby' => 'menu_order',
      'order' => 'ASC',
    ];

    if (class_exists('Polylang')) {
      $liability_atts['lang'] = pll_current_language('slug');
    }

    $liabilities = get_posts($liability_atts);
    $liabilities = PN_FINANCE_MANAGER_Functions_User::pn_finance_manager_filter_user_posts($liabilities, 'pnfm_liability');

    $total_count = count($liabilities);

    if ($total_count === 0) {
      return '<div class="pn-finance-manager-statistics-panel"><p class="pn-finance-manager-statistics-empty">' . esc_html__('No data to display', 'pn-finance-manager') . '</p></div>';
    }

    $liability_types = PN_FINANCE_MANAGER_Data::pn_finance_manager_get_liability_types();
    $counts = [];
    foreach ($liabilities as $liability_id) {
      $type = get_post_meta($liability_id, 'pn_finance_manager_liability_type', true);
      if (empty($type)) {
        $type = 'other';
      }
      if (!isset($counts[$type])) {
        $counts[$type] = 0;
      }
      $counts[$type]++;
    }

    $colors = ['#dc3545', '#fd7e14', '#ffc107', '#007bff', '#6f42c1', '#17a2b8', '#28a745', '#e83e8c', '#6c757d', '#20c997', '#0056b3', '#155724', '#856404'];
    $labels = [];
    $values = [];
    $chart_colors = [];
    $color_index = 0;

    foreach ($counts as $type_key => $count) {
      $labels[] = isset($liability_types[$type_key]) ? $liability_types[$type_key] : ucfirst(str_replace('_', ' ', $type_key));
      $values[] = $count;
      $chart_colors[] = $colors[$color_index % count($colors)];
      $color_index++;
    }

    $chart_data = wp_json_encode([
      'labels' => $labels,
      'values' => $values,
      'colors' => $chart_colors,
    ]);

    ob_start();
    ?>
    <div class="pn-finance-manager-statistics-panel">
      <div class="pn-finance-manager-statistics-metrics">
        <div class="pn-finance-manager-metric-row">
          <div class="pn-finance-manager-metric-item">
            <span class="pn-finance-manager-metric-label"><?php esc_html_e('Total Liabilities', 'pn-finance-manager'); ?></span>
            <span class="pn-finance-manager-metric-value"><?php echo esc_html($total_count); ?></span>
          </div>
        </div>
      </div>
      <div class="pn-finance-manager-statistics-chart-wrapper">
        <canvas class="pn-finance-manager-statistics-chart" data-chart="<?php echo esc_attr($chart_data); ?>"></canvas>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }

  public function pn_finance_manager_liability_list() {
    $liability_atts = [
      'fields' => 'ids',
      'numberposts' => -1,
      'post_type' => 'pnfm_liability',
      'post_status' => 'any', 
      'orderby' => 'menu_order', 
      'order' => 'ASC', 
    ];
    if (class_exists('Polylang')) {
      $liability_atts['lang'] = pll_current_language('slug');
    }
    $liabilities = get_posts($liability_atts);
    
    // Filter liabilities based on user permissions
    $liabilities = PN_FINANCE_MANAGER_Functions_User::pn_finance_manager_filter_user_posts($liabilities, 'pnfm_liability');
    
    ob_start();
    ?>
      <ul class="pn-finance-manager-liabilities pn-finance-manager-list-style-none pn-finance-manager-p-0 pn-finance-manager-margin-auto">
        <?php if (!empty($liabilities)): ?>
          <?php foreach ($liabilities as $liability_id): ?>
            <?php
              $pn_finance_manager_liability_type = get_post_meta($liability_id, 'pn_finance_manager_liability_type', true);
              $liability_types = PN_FINANCE_MANAGER_Data::pn_finance_manager_get_liability_types();
              $liability_type_label = !empty($pn_finance_manager_liability_type) && isset($liability_types[$pn_finance_manager_liability_type]) ? $liability_types[$pn_finance_manager_liability_type] : '';
              $pn_finance_manager_liability_timed_checkbox = get_post_meta($liability_id, 'pn_finance_manager_liability_timed_checkbox', true);
              $pn_finance_manager_liability_period = get_post_meta($liability_id, 'pn_finance_manager_liability_period', true);
            ?>
            <li class="pn-finance-manager-liability pn-finance-manager-pn_finance_manager_liability-list-item pn-finance-manager-mb-10" data-pn_finance_manager_liability-id="<?php echo esc_attr($liability_id); ?>" data-pn-finance-manager-liability-type="<?php echo esc_attr($pn_finance_manager_liability_type); ?>">
              <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-80-percent">
                  <a href="#" class="pn-finance-manager-popup-open-ajax pn-finance-manager-text-decoration-none" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-view" data-pn-finance-manager-ajax-type="pn_finance_manager_liability_view">
                    <span><?php echo esc_html(get_the_title($liability_id)); ?></span>
                    
                    <?php if (!empty($liability_type_label)): ?>
                      <span class="pn-finance-manager-liability-type-badge pn-finance-manager-ml-10 pn-finance-manager-p-5 pn-finance-manager-font-size-12 pn-finance-manager-border-radius-3" style="background-color: #f0f0f0; color: #666;">
                        <?php echo esc_html($liability_type_label); ?>
                      </span>
                    <?php endif ?>

                    <?php if ($pn_finance_manager_liability_timed_checkbox == 'on'): ?>
                      <i class="material-icons-outlined pn-finance-manager-timed pn-finance-manager-cursor-pointer pn-finance-manager-vertical-align-super pn-finance-manager-p-5 pn-finance-manager-font-size-15 pn-finance-manager-tooltip" title="<?php esc_html_e('This Liability is timed', 'pn-finance-manager'); ?>">access_time</i>
                    <?php endif ?>

                    <?php if ($pn_finance_manager_liability_period == 'on'): ?>
                      <i class="material-icons-outlined pn-finance-manager-timed pn-finance-manager-cursor-pointer pn-finance-manager-vertical-align-super pn-finance-manager-p-5 pn-finance-manager-font-size-15 pn-finance-manager-tooltip" title="<?php esc_html_e('This Liability is periodic', 'pn-finance-manager'); ?>">replay</i>
                    <?php endif ?>
                  </a>
                </div>
                <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-text-align-right pn-finance-manager-position-relative">
                  <i class="material-icons-outlined pn-finance-manager-menu-more-btn pn-finance-manager-cursor-pointer pn-finance-manager-vertical-align-middle pn-finance-manager-font-size-30">more_vert</i>
                  <div class="pn-finance-manager-menu-more pn-finance-manager-z-index-99 pn-finance-manager-display-none-soft">
                    <ul class="pn-finance-manager-list-style-none">
                      <li>
                        <a href="#" class="pn-finance-manager-popup-open-ajax pn-finance-manager-text-decoration-none" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-view" data-pn-finance-manager-ajax-type="pn_finance_manager_liability_view">
                          <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-70-percent">
                              <p><?php esc_html_e('View Liability', 'pn-finance-manager'); ?></p>
                            </div>
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-finance-manager-vertical-align-middle pn-finance-manager-font-size-30 pn-finance-manager-ml-30">visibility</i>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="pn-finance-manager-popup-open-ajax pn-finance-manager-text-decoration-none" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-edit" data-pn-finance-manager-ajax-type="pn_finance_manager_liability_edit"> 
                          <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-70-percent">
                              <p><?php esc_html_e('Edit Liability', 'pn-finance-manager'); ?></p>
                            </div>
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-finance-manager-vertical-align-middle pn-finance-manager-font-size-30 pn-finance-manager-ml-30">edit</i>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="pn-finance-manager-pn_finance_manager_liability-duplicate-post">
                          <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-70-percent">
                              <p><?php esc_html_e('Duplicate Liability', 'pn-finance-manager'); ?></p>
                            </div>
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-finance-manager-vertical-align-middle pn-finance-manager-font-size-30 pn-finance-manager-ml-30">copy</i>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="pn-finance-manager-popup-open" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-remove">
                          <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-70-percent">
                              <p><?php esc_html_e('Remove Liability', 'pn-finance-manager'); ?></p>
                            </div>
                            <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-text-align-right">
                              <i class="material-icons-outlined pn-finance-manager-vertical-align-middle pn-finance-manager-font-size-30 pn-finance-manager-ml-30">delete</i>
                            </div>
                          </div>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach ?>
        <?php endif ?>
        <li class="pn-finance-manager-mt-50 pn-finance-manager-liability" data-pn_finance_manager_liability-id="0">
        <?php if (is_user_logged_in()) : ?>
          <a href="#" class="pn-finance-manager-popup-open-ajax pn-finance-manager-text-decoration-none" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-add" data-pn-finance-manager-ajax-type="pn_finance_manager_liability_new">
            <div class="pn-finance-manager-display-table pn-finance-manager-width-100-percent">
              <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-20-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent pn-finance-manager-text-align-center">
                <i class="material-icons-outlined pn-finance-manager-cursor-pointer pn-finance-manager-vertical-align-middle pn-finance-manager-font-size-30 pn-finance-manager-width-25">add</i>
              </div>
              <div class="pn-finance-manager-display-inline-table pn-finance-manager-width-80-percent pn-finance-manager-tablet-display-block pn-finance-manager-tablet-width-100-percent">
                <?php esc_html_e('Add new Liability', 'pn-finance-manager'); ?>
              </div>
            </div>
          </a>
        <?php endif; ?>
        </li>
      </ul>
    <?php
    $return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $return_string;
  }

  public function pn_finance_manager_liability_view($liability_id) {
    ob_start();
    self::pn_finance_manager_liability_register_scripts();
    self::pn_finance_manager_liability_print_scripts();
    $current_liability_type = get_post_meta($liability_id, 'pn_finance_manager_liability_type', true);
    ?>
      <div class="pn-finance-manager-liability-view pn-finance-manager-p-30" data-pn_finance_manager_liability-id="<?php echo esc_attr($liability_id); ?>">
        <h4 class="pn-finance-manager-text-align-center"><?php echo esc_html(get_the_title($liability_id)); ?></h4>
        <div class="pn-finance-manager-word-wrap-break-word">
          <p><?php echo wp_kses(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_post($liability_id)->post_content)), PN_FINANCE_MANAGER_KSES); ?></p>
        </div>
        <div class="pn-finance-manager-liability-view">
          <?php foreach (array_merge(self::pn_finance_manager_liability_get_fields(), self::pn_finance_manager_liability_get_fields_meta_with_values($liability_id)) as $field): ?>
            <?php if (!empty($field['parent_option']) && $field['parent_option'] !== $current_liability_type) continue; ?>
            <?php echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_display_wrapper($field, 'post', $liability_id), PN_FINANCE_MANAGER_KSES); ?>
          <?php endforeach ?>
          <div class="pn-finance-manager-text-align-right pn-finance-manager-liability" data-pn_finance_manager_liability-id="<?php echo esc_attr($liability_id); ?>">
            <a href="#" class="pn-finance-manager-btn pn-finance-manager-btn-mini pn-finance-manager-popup-open-ajax" data-pn-finance-manager-popup-id="pn-finance-manager-popup-pn_finance_manager_liability-edit" data-pn-finance-manager-ajax-type="pn_finance_manager_liability_edit"><?php esc_html_e('Edit Liability', 'pn-finance-manager'); ?></a>
          </div>
        </div>
      </div>
    <?php
    $return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $return_string;
  }

  public function pn_finance_manager_liability_new() {
    if (!is_user_logged_in()) {
      wp_die(esc_html__('You must be logged in to create a new liability.', 'pn-finance-manager'), esc_html__('Access Denied', 'pn-finance-manager'), ['response' => 403]);
    }
    ob_start();
    self::pn_finance_manager_liability_register_scripts();
    self::pn_finance_manager_liability_print_scripts();
    ?>
      <div class="pn-finance-manager-liability-new pn-finance-manager-p-30">
        <h4 class="pn-finance-manager-mb-30"><?php esc_html_e('Add new Liability', 'pn-finance-manager'); ?></h4>
        <form action="" method="post" id="pn-finance-manager-form" class="pn-finance-manager-form">
          <?php
            $liability_fields = self::pn_finance_manager_liability_get_fields();
            // Render title first
            if (isset($liability_fields['pn_finance_manager_liability_title'])) {
              echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($liability_fields['pn_finance_manager_liability_title'], 'post'), PN_FINANCE_MANAGER_KSES);
            }
          ?>
          <?php foreach (self::pn_finance_manager_liability_get_fields_meta() as $field_meta): ?>
            <?php echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($field_meta, 'post'), PN_FINANCE_MANAGER_KSES); ?>
          <?php endforeach ?>
          <?php
            // Render description/comments last
            if (isset($liability_fields['pn_finance_manager_liability_description'])) {
              echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($liability_fields['pn_finance_manager_liability_description'], 'post'), PN_FINANCE_MANAGER_KSES);
            }
          ?>
          <div class="pn-finance-manager-text-align-right">
            <input class="pn-finance-manager-btn" data-pn-finance-manager-type="post" data-pn-finance-manager-subtype="post_new" data-pn-finance-manager-post-type="pnfm_liability" type="submit" value="<?php esc_attr_e('Create Liability', 'pn-finance-manager'); ?>"/>
          </div>
        </form> 
      </div>
    <?php
    $return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $return_string;
  }

  public function pn_finance_manager_liability_edit($liability_id) {
    ob_start();
    self::pn_finance_manager_liability_register_scripts();
    self::pn_finance_manager_liability_print_scripts();
    ?>
        <div class="pn-finance-manager-liability-edit pn-finance-manager-p-30">
        <p class="pn-finance-manager-text-align-center pn-finance-manager-mb-0"><?php esc_html_e('Editing', 'pn-finance-manager'); ?></p>
        <h4 class="pn-finance-manager-text-align-center pn-finance-manager-mb-30"><?php echo esc_html(get_the_title($liability_id)); ?></h4>
        <form action="" method="post" id="pn-finance-manager-form" class="pn-finance-manager-form">
          <?php
            $liability_fields = self::pn_finance_manager_liability_get_fields($liability_id);
            // Render title first
            if (isset($liability_fields['pn_finance_manager_liability_title'])) {
              echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($liability_fields['pn_finance_manager_liability_title'], 'post', $liability_id), PN_FINANCE_MANAGER_KSES);
            }
          ?>
          <?php foreach (self::pn_finance_manager_liability_get_fields_meta_with_values($liability_id) as $field_meta): ?>
            <?php echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($field_meta, 'post', $liability_id), PN_FINANCE_MANAGER_KSES); ?>
          <?php endforeach ?>
          <?php
            // Render description/comments last
            if (isset($liability_fields['pn_finance_manager_liability_description'])) {
              echo wp_kses(PN_FINANCE_MANAGER_Forms::pn_finance_manager_input_wrapper_builder($liability_fields['pn_finance_manager_liability_description'], 'post', $liability_id), PN_FINANCE_MANAGER_KSES);
            }
          ?>
          <div class="pn-finance-manager-text-align-right">
            <input class="pn-finance-manager-btn" type="submit" data-pn-finance-manager-type="post" data-pn-finance-manager-subtype="post_edit" data-pn-finance-manager-post-type="pnfm_liability" data-pn-finance-manager-post-id="<?php echo esc_attr($liability_id); ?>" value="<?php esc_attr_e('Save Liability', 'pn-finance-manager'); ?>"/>
          </div>
        </form> 
      </div>
    <?php
    $return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $return_string;
  }
} 