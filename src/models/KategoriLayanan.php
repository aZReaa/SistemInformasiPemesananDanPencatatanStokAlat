<?php

class KategoriLayanan
{
    private $db;
    private $id_kategori;
    private $nama_kategori;
    private $deskripsi;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Setters sesuai UML attributes
    public function setIdKategori($id_kategori)
    {
        $this->id_kategori = $id_kategori;
    }
    
    public function setNamaKategori($nama_kategori)
    {
        $this->nama_kategori = $nama_kategori;
    }
    
    public function setDeskripsi($deskripsi)
    {
        $this->deskripsi = $deskripsi;
    }
    
    // Getters
    public function getIdKategori()
    {
        return $this->id_kategori;
    }
    
    public function getNamaKategori()
    {
        return $this->nama_kategori;
    }
    
    public function getDeskripsi()
    {
        return $this->deskripsi;
    }
    
    /**
     * Insert kategori baru
     * @param array $data
     * @return bool|int
     */
    public function insert($data)
    {
        try {
            $sql = "INSERT INTO kategori_layanan (nama_kategori, deskripsi) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['nama_kategori'],
                $data['deskripsi']
            ]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error inserting kategori: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update kategori
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE kategori_layanan SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['nama_kategori'],
                $data['deskripsi'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating kategori: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete kategori
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            // Check if kategori sedang digunakan
            $checkSql = "SELECT COUNT(*) FROM alat WHERE id_kategori = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$id]);
            
            if ($checkStmt->fetchColumn() > 0) {
                return false; // Kategori sedang digunakan
            }
            
            $sql = "DELETE FROM kategori_layanan WHERE id_kategori = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting kategori: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get kategori by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM kategori_layanan WHERE id_kategori = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting kategori by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all kategori
     * @return array
     */
    public function getAll()
    {
        try {
            $sql = "SELECT * FROM kategori_layanan ORDER BY nama_kategori ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all kategori: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get kategori with count alat
     * @return array
     */
    public function getAllWithAlatCount()
    {
        try {
            $sql = "
                SELECT k.*, COUNT(a.id_alat) as jumlah_alat
                FROM kategori_layanan k
                LEFT JOIN alat a ON k.id_kategori = a.id_kategori
                GROUP BY k.id_kategori
                ORDER BY k.nama_kategori ASC
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting kategori with alat count: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search kategori
     * @param string $keyword
     * @return array
     */
    public function search($keyword)
    {
        try {
            $sql = "SELECT * FROM kategori_layanan WHERE nama_kategori LIKE ? OR deskripsi LIKE ? ORDER BY nama_kategori ASC";
            $stmt = $this->db->prepare($sql);
            $keyword = "%$keyword%";
            $stmt->execute([$keyword, $keyword]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error searching kategori: " . $e->getMessage());
            return [];
        }
    }
}
