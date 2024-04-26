<?php
/**
 * Report Repository.
 */

namespace App\Repository;

/**
 * Class ReportRepository.
 */
class ReportRepository
{
    private array $reports = [
        1 => [
            'id' => 1,
            'title' => 'User Registration Error',
            'content' => 'When attempting to register a new user on the website, users are encountering an error message stating "Invalid email format" even when entering a valid email address. This prevents new users from successfully registering and accessing the platform.',
            'status' => 'unresolved',
        ],
        2 => [
            'id' => 2,
            'title' => 'Missing Product Images',
            'content' => 'In the product catalog section of the website, several product listings are missing their associated images. Instead, placeholder icons are displayed, making it difficult for users to identify and differentiate between products. This issue affects the user experience and may lead to confusion and decreased sales.',
            'status' => 'resolved',
        ],
        3 => [
            'id' => 3,
            'title' => 'Payment Processing Failure',
            'content' => 'Users are reporting issues with completing payments during the checkout process. After entering payment information and clicking "Submit", the page either freezes indefinitely or displays an error message indicating that the transaction cannot be processed. This prevents users from completing purchases and negatively impacts revenue generation.',
            'status' => 'unresolved',
        ],
        4 => [
            'id' => 4,
            'title' => 'Incorrect Data Displayed in Dashboard',
            'content' => 'In the user dashboard section, certain data fields are displaying incorrect or outdated information. For example, the "Total Orders" counter shows a significantly lower number than the actual number of orders placed by the user. This discrepancy in data accuracy undermines the reliability of the dashboard feature and may lead to misinformed decision-making by users.',
            'status' => 'unresolved',
        ],
    ];

    /**
     * Find all.
     *
     * @return array[] Result
     */
    public function findAll(): array
    {
        return $this->reports;
    }

    /**
     * Find one.
     *
     * @param int $id ID
     *
     * @return array|null Result
     */
    public function findOneById(int $id): ?array
    {
        return (count($this->reports) && isset($this->reports[$id])) ? $this->reports[$id] : null;
    }
}
