<?php
require_once __DIR__ . '/../../function/connection.php';

class RawMaterialCalculator {
    private $db;
    private const STANDARD_WIDTH = 0.762; // Standard width in meters
    private const STANDARD_LENGTH = 1.0; // Standard length in meters
    private const STANDARD_PINA_LOOSE = 20.000; // Standard Piña Loose grams for Piña Seda (1m²)
    private const STANDARD_SILK = 9.000; // Standard Silk grams for Piña Seda (1m²)
    private const STANDARD_PINA_LOOSE_PURE = 30.000; // Standard Piña Loose grams for Pure Piña Cloth (1m²)
    private const STANDARD_AREA = 1.0; // Standard area in m² (1m × 1m)
    private const KNOTTED_LINIWAN_RATE = 1.4; // Consumption rate for Knotted Liniwan (g/g)
    private const KNOTTED_BASTOS_RATE = 1.15; // Consumption rate for Knotted Bastos (g/g)
    private const WARPED_SILK_RATE = 1.14; // Consumption rate for Warped Silk (g/g)

    public function __construct($db) {
        $this->db = $db;
    }

    public function calculateAndDeductMaterials($productName, $quantity, $length = null, $width = null, $weight = null) {
        try {
            if ($productName === 'Piña Seda') {
                if ($length === null || $width === null) {
                    throw new Exception("Length and width are required for Piña Seda");
                }

                // Calculate scaling factor based on actual area vs standard area (1m²)
                $actualArea = $length * $width;
                $scalingFactor = $actualArea / self::STANDARD_AREA;

                // Calculate required amounts with decimal precision
                $pinaLooseAmount = round(self::STANDARD_PINA_LOOSE * $scalingFactor * $quantity, 3);
                $silkAmount = round(self::STANDARD_SILK * $scalingFactor * $quantity, 3);

                $materialDeductions = [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Bastos',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::STANDARD_PINA_LOOSE,
                            'unit' => 'g/m²',
                            'quantity' => $quantity,
                            'dimensions' => sprintf('%.3fm × %.3fm', $length, $width),
                            'scaling_factor' => $scalingFactor
                        ]
                    ],
                    [
                        'name' => 'Silk',
                        'category' => null,
                        'amount' => $silkAmount,
                        'details' => [
                            'rate' => self::STANDARD_SILK,
                            'unit' => 'g/m²',
                            'quantity' => $quantity,
                            'dimensions' => sprintf('%.3fm × %.3fm', $length, $width),
                            'scaling_factor' => $scalingFactor
                        ]
                    ]
                ];

                // Deduct materials from inventory
                foreach ($materialDeductions as $deduction) {
                    $this->deductMaterial(
                        $deduction['name'],
                        $deduction['category'],
                        $deduction['amount']
                    );
                }

                return [
                    'success' => true,
                    'deductions' => $materialDeductions
                ];
            } else if ($productName === 'Pure Piña Cloth') {
                if ($length === null || $width === null) {
                    throw new Exception("Length and width are required for Pure Piña Cloth");
                }

                // Calculate scaling factor based on actual area vs standard area
                $actualArea = $length * $width;
                $scalingFactor = $actualArea / self::STANDARD_AREA;

                // Calculate required amount with decimal precision
                $pinaLooseAmount = round(self::STANDARD_PINA_LOOSE_PURE * $scalingFactor * $quantity, 2);

                $materialDeductions = [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Liniwan/Washout',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::STANDARD_PINA_LOOSE_PURE,
                            'unit' => 'g/pc',
                            'quantity' => $quantity,
                            'dimensions' => sprintf('%.3fm × %.3fm', $length, $width),
                            'scaling_factor' => $scalingFactor
                        ]
                    ]
                ];

                // Deduct materials from inventory
                foreach ($materialDeductions as $deduction) {
                    $this->deductMaterial(
                        $deduction['name'],
                        $deduction['category'],
                        $deduction['amount']
                    );
                }

                return [
                    'success' => true,
                    'deductions' => $materialDeductions
                ];
            } else if ($productName === 'Knotted Liniwan') {
                if ($weight === null) {
                    throw new Exception("Weight is required for Knotted Liniwan");
                }

                // Calculate total production weight and material needed
                $totalProductionWeight = $weight * $quantity;
                $pinaLooseAmount = $totalProductionWeight * self::KNOTTED_LINIWAN_RATE;

                $materialDeductions = [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Liniwan/Washout',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::KNOTTED_LINIWAN_RATE,
                            'unit' => 'g/g',
                            'quantity' => $quantity,
                            'weight' => sprintf('%.2fg', $weight),
                            'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                        ]
                    ]
                ];

                // Deduct materials from inventory
                foreach ($materialDeductions as $deduction) {
                    $this->deductMaterial(
                        $deduction['name'],
                        $deduction['category'],
                        $deduction['amount']
                    );
                }

                return [
                    'success' => true,
                    'deductions' => $materialDeductions
                ];
            } else if ($productName === 'Knotted Bastos') {
                if ($weight === null) {
                    throw new Exception("Weight is required for Knotted Bastos");
                }

                // Calculate total production weight and material needed
                $totalProductionWeight = $weight * $quantity;
                $pinaLooseAmount = $totalProductionWeight * self::KNOTTED_BASTOS_RATE;

                $materialDeductions = [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Bastos',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::KNOTTED_BASTOS_RATE,
                            'unit' => 'g/g',
                            'quantity' => $quantity,
                            'weight' => sprintf('%.2fg', $weight),
                            'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                        ]
                    ]
                ];

                // Deduct materials from inventory
                foreach ($materialDeductions as $deduction) {
                    $this->deductMaterial(
                        $deduction['name'],
                        $deduction['category'],
                        $deduction['amount']
                    );
                }

                return [
                    'success' => true,
                    'deductions' => $materialDeductions
                ];
            } else if ($productName === 'Warped Silk') {
                if ($weight === null) {
                    throw new Exception("Weight is required for Warped Silk");
                }

                // Calculate total production weight and material needed
                $totalProductionWeight = $weight * $quantity;
                $silkAmount = $totalProductionWeight * self::WARPED_SILK_RATE;

                $materialDeductions = [
                    [
                        'name' => 'Silk',
                        'category' => null,
                        'amount' => $silkAmount,
                        'details' => [
                            'rate' => self::WARPED_SILK_RATE,
                            'unit' => 'g/g',
                            'quantity' => $quantity,
                            'weight' => sprintf('%.2fg', $weight),
                            'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                        ]
                    ]
                ];

                // Deduct materials from inventory
                foreach ($materialDeductions as $deduction) {
                    $this->deductMaterial(
                        $deduction['name'],
                        $deduction['category'],
                        $deduction['amount']
                    );
                }

                return [
                    'success' => true,
                    'deductions' => $materialDeductions
                ];
            } else {
                // For other products, use the existing logic
                $query = "SELECT * FROM product_raw_materials WHERE product_name = ?";
                $stmt = $this->db->conn->prepare($query);
                $stmt->bind_param("s", $productName);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $materialDeductions = [];
                
                while ($requirement = $result->fetch_assoc()) {
                    $consumptionRate = $requirement['consumption_rate'];
                    $consumptionUnit = $requirement['consumption_unit'];
                    
                    // Calculate required amount based on product type and unit
                    $requiredAmount = 0;
                    
                    if ($consumptionUnit === 'g/g' && $weight !== null) {
                        $requiredAmount = $consumptionRate * $weight * $quantity;
                    }
                    
                    if ($requiredAmount > 0) {
                        $materialDeductions[] = [
                            'name' => $requirement['raw_material_name'],
                            'category' => $requirement['raw_material_category'],
                            'amount' => $requiredAmount,
                            'details' => [
                                'rate' => $consumptionRate,
                                'unit' => $consumptionUnit,
                                'quantity' => $quantity,
                                'weight' => $weight !== null ? sprintf('%.3fg', $weight) : null
                            ]
                        ];
                        
                        $this->deductMaterial(
                            $requirement['raw_material_name'],
                            $requirement['raw_material_category'],
                            $requiredAmount
                        );
                    }
                }
                
                return [
                    'success' => true,
                    'deductions' => $materialDeductions
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function deductMaterial($materialName, $category, $amount) {
        // Debug log
        error_log(sprintf(
            "Deducting material - Name: %s, Category: %s, Amount: %.3f",
            $materialName,
            $category ?? 'NULL',
            $amount
        ));

        // Prepare the query with category condition
        if ($category === null) {
            $query = "UPDATE raw_materials 
                     SET rm_quantity = rm_quantity - ? 
                     WHERE raw_materials_name = ? 
                     AND (category IS NULL OR category = '')";
            $stmt = $this->db->conn->prepare($query);
            $stmt->bind_param("ds", $amount, $materialName);
        } else {
            $query = "UPDATE raw_materials 
                     SET rm_quantity = rm_quantity - ? 
                     WHERE raw_materials_name = ? 
                     AND category = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->bind_param("dss", $amount, $materialName, $category);
        }

        // Execute the update
        if (!$stmt->execute()) {
            throw new Exception("Failed to deduct material: " . $stmt->error);
        }

        // Check if any rows were affected
        if ($stmt->affected_rows === 0) {
            throw new Exception("No matching material found for deduction");
        }

        // Debug log the result
        error_log(sprintf(
            "Material deducted successfully - Rows affected: %d",
            $stmt->affected_rows
        ));

        $stmt->close();
    }

    public function calculateMaterialsNeeded($productName, $quantity, $length = null, $width = null, $weight = null) {
        try {
            if ($productName === 'Piña Seda') {
                if ($length === null || $width === null) {
                    throw new Exception("Length and width are required for Piña Seda");
                }

                // Calculate scaling factor based on actual area vs standard area (1m²)
                $actualArea = $length * $width;
                $scalingFactor = $actualArea / self::STANDARD_AREA;

                // Calculate required amounts with decimal precision
                $pinaLooseAmount = round(self::STANDARD_PINA_LOOSE * $scalingFactor * $quantity, 3);
                $silkAmount = round(self::STANDARD_SILK * $scalingFactor * $quantity, 3);

                return [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Bastos',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::STANDARD_PINA_LOOSE,
                            'unit' => 'g/m²',
                            'quantity' => $quantity,
                            'dimensions' => sprintf('%.3fm × %.3fm', $length, $width),
                            'scaling_factor' => $scalingFactor
                        ]
                    ],
                    [
                        'name' => 'Silk',
                        'category' => null,
                        'amount' => $silkAmount,
                        'details' => [
                            'rate' => self::STANDARD_SILK,
                            'unit' => 'g/m²',
                            'quantity' => $quantity,
                            'dimensions' => sprintf('%.3fm × %.3fm', $length, $width),
                            'scaling_factor' => $scalingFactor
                        ]
                    ]
                ];
            } else if ($productName === 'Pure Piña Cloth') {
                if ($length === null || $width === null) {
                    throw new Exception("Length and width are required for Pure Piña Cloth");
                }

                // Calculate scaling factor based on actual area vs standard area
                $actualArea = $length * $width;
                $scalingFactor = $actualArea / self::STANDARD_AREA;

                // Calculate required amount with decimal precision
                $pinaLooseAmount = round(self::STANDARD_PINA_LOOSE_PURE * $scalingFactor * $quantity, 2);

                return [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Liniwan/Washout',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::STANDARD_PINA_LOOSE_PURE,
                            'unit' => 'g/m²',
                            'quantity' => $quantity,
                            'dimensions' => sprintf('%.3fm × %.3fm', $length, $width),
                            'scaling_factor' => $scalingFactor
                        ]
                    ]
                ];
            } else if ($productName === 'Knotted Liniwan') {
                if ($weight === null) {
                    throw new Exception("Weight is required for Knotted Liniwan");
                }

                // Calculate total production weight and material needed
                $totalProductionWeight = $weight * $quantity;
                $pinaLooseAmount = $totalProductionWeight * self::KNOTTED_LINIWAN_RATE;

                return [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Liniwan/Washout',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::KNOTTED_LINIWAN_RATE,
                            'unit' => 'g/g',
                            'quantity' => $quantity,
                            'weight' => sprintf('%.2fg', $weight),
                            'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                        ]
                    ]
                ];
            } else if ($productName === 'Knotted Bastos') {
                if ($weight === null) {
                    throw new Exception("Weight is required for Knotted Bastos");
                }

                // Calculate total production weight and material needed
                $totalProductionWeight = $weight * $quantity;
                $pinaLooseAmount = $totalProductionWeight * self::KNOTTED_BASTOS_RATE;

                return [
                    [
                        'name' => 'Piña Loose',
                        'category' => 'Bastos',
                        'amount' => $pinaLooseAmount,
                        'details' => [
                            'rate' => self::KNOTTED_BASTOS_RATE,
                            'unit' => 'g/g',
                            'quantity' => $quantity,
                            'weight' => sprintf('%.2fg', $weight),
                            'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                        ]
                    ]
                ];
            } else if ($productName === 'Warped Silk') {
                if ($weight === null) {
                    throw new Exception("Weight is required for Warped Silk");
                }

                // Calculate total production weight and material needed
                $totalProductionWeight = $weight * $quantity;
                $silkAmount = $totalProductionWeight * self::WARPED_SILK_RATE;

                return [
                    [
                        'name' => 'Silk',
                        'category' => null,
                        'amount' => $silkAmount,
                        'details' => [
                            'rate' => self::WARPED_SILK_RATE,
                            'unit' => 'g/g',
                            'quantity' => $quantity,
                            'weight' => sprintf('%.2fg', $weight),
                            'total_weight' => sprintf('%.2fg', $totalProductionWeight)
                        ]
                    ]
                ];
            } else {
                // For other products, use the existing logic
                $query = "SELECT * FROM product_raw_materials WHERE product_name = ?";
                $stmt = $this->db->conn->prepare($query);
                $stmt->bind_param("s", $productName);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $materialRequirements = [];
                
                while ($requirement = $result->fetch_assoc()) {
                    $consumptionRate = $requirement['consumption_rate'];
                    $consumptionUnit = $requirement['consumption_unit'];
                    
                    // Calculate required amount based on product type and unit
                    $requiredAmount = 0;
                    
                    if ($consumptionUnit === 'g/g' && $weight !== null) {
                        $requiredAmount = round($consumptionRate * $weight * $quantity, 2);
                    }
                    
                    if ($requiredAmount > 0) {
                        $materialRequirements[] = [
                            'name' => $requirement['raw_material_name'],
                            'category' => $requirement['raw_material_category'],
                            'amount' => $requiredAmount,
                            'details' => [
                                'rate' => $consumptionRate,
                                'unit' => $consumptionUnit,
                                'quantity' => $quantity,
                                'weight' => $weight !== null ? sprintf('%.3fg', $weight) : null
                            ]
                        ];
                    }
                }
                
                return $materialRequirements;
            }
        } catch (Exception $e) {
            error_log("Error calculating materials needed: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private function checkMaterialAvailability($materialName, $category, $amountNeeded) {
        // Query to check available quantity
        if ($category === null) {
            $query = "SELECT rm_quantity FROM raw_materials 
                     WHERE raw_materials_name = ? 
                     AND (category IS NULL OR category = '')";
            $stmt = $this->db->conn->prepare($query);
            $stmt->bind_param("s", $materialName);
        } else {
            $query = "SELECT rm_quantity FROM raw_materials 
                     WHERE raw_materials_name = ? 
                     AND category = ?";
            $stmt = $this->db->conn->prepare($query);
            $stmt->bind_param("ss", $materialName, $category);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Material not found: $materialName" . ($category ? " ($category)" : ""));
        }

        $row = $result->fetch_assoc();
        $availableQuantity = $row['rm_quantity'];

        if ($availableQuantity < $amountNeeded) {
            throw new Exception(sprintf(
                "Insufficient stock for %s%s. Required: %.0fg, Available: %.0fg",
                $materialName,
                $category ? " ($category)" : "",
                $amountNeeded,
                $availableQuantity
            ));
        }

        return true;
    }

    public function validateMaterialAvailability($productName, $quantity, $length = null, $width = null, $weight = null) {
        // Calculate required materials without deducting
        $materials = $this->calculateMaterialsNeeded($productName, $quantity, $length, $width, $weight);
        
        // Check availability for each material
        foreach ($materials as $material) {
            $this->checkMaterialAvailability(
                $material['name'],
                $material['category'],
                $material['amount']
            );
        }
        
        return true;
    }
} 