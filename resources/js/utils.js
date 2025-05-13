/**
 * Formata uma data para exibição no formato dd/mm/yyyy
 * @param {Date|string} date - Data a ser formatada
 * @returns {string} - Data formatada
 */
export function formatDate(date) {
  if (!date) return '';
  
  if (typeof date === 'string') {
    // Se já estiver no formato yyyy-mm-dd, convertemos para Date
    date = new Date(date);
  }
  
  return new Intl.DateTimeFormat('pt-PT', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(date);
}

/**
 * Formata uma data e hora para exibição no formato dd/mm/yyyy às HH:MM
 * @param {Date|string} date - Data e hora a ser formatada
 * @returns {string} - Data e hora formatada
 */
export function formatDateTime(date) {
  if (!date) return '';
  
  if (typeof date === 'string') {
    date = new Date(date);
  }
  
  const dateStr = formatDate(date);
  const timeStr = new Intl.DateTimeFormat('pt-PT', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
  
  return `${dateStr} às ${timeStr}`;
}

/**
 * Formata um valor monetário
 * @param {number} value - Valor a ser formatado
 * @param {string} currency - Moeda (default: EUR)
 * @returns {string} - Valor formatado
 */
export function formatCurrency(value, currency = 'EUR') {
  if (value === null || value === undefined) return '—';
  
  return new Intl.NumberFormat('pt-PT', {
    style: 'currency',
    currency: currency
  }).format(value);
} 