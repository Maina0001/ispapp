/**
 * Returns MikroTik-Rate-Limit string
 * Format: "Rx/Tx BurstRx/BurstTx ThresholdRx/ThresholdTx TimeRx/TimeTx Priority LimitAtRx/LimitAtTx"
 * Simple Format: "5M/10M" (Upload/Download)
 */
public function getMikrotikRateLimit(): string
{
    // Convert kbps to 'M' or 'K' for MikroTik
    $up = ($this->upload_speed >= 1024) ? ($this->upload_speed / 1024) . 'M' : $this->upload_speed . 'K';
    $down = ($this->download_speed >= 1024) ? ($this->download_speed / 1024) . 'M' : $this->download_speed . 'K';
    
    return "{$up}/{$down}";
}