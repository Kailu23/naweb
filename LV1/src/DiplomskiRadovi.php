<?php
include_once 'iRadovi.php';
class DiplomskiRadovi implements iRadovi
{
    private $naziv_rada = NULL;
    private $tekst_rada = NULL;
    private $link_rada = NULL;
    private $oib_tvrtke = NULL;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->create($data);
        }
    }
    public function get_naziv_rada()
    {
        return $this->naziv_rada;
    }
    public function get_tekst_rada()
    {
        return $this->tekst_rada;
    }
    public function get_link_rada()
    {
        return $this->link_rada;
    }
    public function get_oib_tvrtke()
    {
        return $this->oib_tvrtke;
    }
    #[Override]
    public function create($data)
    {
        $this->naziv_rada = $data['naziv_rada'] ?? '';
        $this->tekst_rada = $data['tekst_rada'] ?? '';
        $this->link_rada = $data['link_rada'] ?? '';
        $this->oib_tvrtke = $data['oib_tvrtke'] ?? '';
    }
    #[Override]
    public function read()
    {
        return [
            'naziv_rada' => $this->naziv_rada,
            'tekst_rada' => $this->tekst_rada,
            'link_rada' => $this->link_rada,
            'oib_tvrtke' => $this->oib_tvrtke
        ];
    }
    #[Override]
    public function save()
    {
        throw new \Exception('Not implemented');
    }
}
