/**
 * ANALYTICS MODULE
 * Handles productivity analytics and statistics visualization
 */

(function() {
  'use strict';

  // ===== CHART RENDERING =====

  /**
   * Create a simple bar chart using HTML/CSS
   */
  function createBarChart(data, container, options = {}) {
    if (!container) return;

    const maxValue = Math.max(...Object.values(data));
    const colors = options.colors || ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'];
    
    let html = '<div style="display: flex; align-items: flex-end; gap: 15px; height: 250px; padding: 20px; background: #fff; border-radius: 8px;">';
    
    Object.entries(data).forEach(([label, value], index) => {
      const percentage = maxValue > 0 ? (value / maxValue) * 100 : 0;
      const color = colors[index % colors.length];
      
      html += `
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px;">
          <div style="font-weight: bold; font-size: 18px; color: ${color};">${value}</div>
          <div style="width: 100%; height: ${percentage}%; background: ${color}; border-radius: 4px 4px 0 0; min-height: 5px; transition: height 0.5s ease;"></div>
          <div style="font-size: 12px; text-align: center; color: #666;">${label}</div>
        </div>
      `;
    });
    
    html += '</div>';
    container.innerHTML = html;
  }

  /**
   * Create a simple pie chart using conic gradient
   */
  function createPieChart(data, container, options = {}) {
    if (!container) return;

    const total = Object.values(data).reduce((sum, val) => sum + val, 0);
    const colors = options.colors || ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'];
    
    if (total === 0) {
      container.innerHTML = '<p style="text-align: center; color: #999;">No data available</p>';
      return;
    }

    let gradientStops = [];
    let currentPercentage = 0;

    Object.entries(data).forEach(([label, value], index) => {
      const percentage = (value / total) * 100;
      const color = colors[index % colors.length];
      
      gradientStops.push(`${color} ${currentPercentage}% ${currentPercentage + percentage}%`);
      currentPercentage += percentage;
    });

    let html = `
      <div style="display: flex; gap: 30px; align-items: center; padding: 20px; background: #fff; border-radius: 8px;">
        <div style="width: 200px; height: 200px; border-radius: 50%; background: conic-gradient(${gradientStops.join(', ')}); box-shadow: 0 4px 15px rgba(0,0,0,0.1);"></div>
        <div style="flex: 1;">
    `;

    Object.entries(data).forEach(([label, value], index) => {
      const percentage = ((value / total) * 100).toFixed(1);
      const color = colors[index % colors.length];
      
      html += `
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <div style="width: 20px; height: 20px; background: ${color}; border-radius: 3px;"></div>
          <div style="flex: 1;">
            <strong>${label}</strong>: ${value} (${percentage}%)
          </div>
        </div>
      `;
    });

    html += '</div></div>';
    container.innerHTML = html;
  }

  /**
   * Create statistics cards
   */
  function createStatsCards(stats, container) {
    if (!container) return;

    const cards = [
      { label: 'Total Tasks', value: stats.total, color: '#007bff', icon: '📋' },
      { label: 'Completed', value: stats.completed, color: '#28a745', icon: '✅' },
      { label: 'In Progress', value: stats.inProgress, color: '#ffc107', icon: '⏳' },
      { label: 'Pending', value: stats.pending, color: '#6c757d', icon: '⏸️' },
      { label: 'Overdue', value: stats.overdue, color: '#dc3545', icon: '⚠️' },
      { label: 'Avg Progress', value: `${stats.averageProgress}%`, color: '#17a2b8', icon: '📊' }
    ];

    let html = '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">';

    cards.forEach(card => {
      html += `
        <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid ${card.color};">
          <div style="font-size: 24px; margin-bottom: 8px;">${card.icon}</div>
          <div style="font-size: 28px; font-weight: bold; color: ${card.color}; margin-bottom: 5px;">${card.value}</div>
          <div style="color: #666; font-size: 14px;">${card.label}</div>
        </div>
      `;
    });

    html += '</div>';
    container.innerHTML = html;
  }

  /**
   * Render complete analytics dashboard
   */
  function renderAnalyticsDashboard() {
    const statsContainer = document.getElementById('statsCards');
    const taskTypeChart = document.getElementById('taskTypeChart');
    const statusChart = document.getElementById('statusChart');
    const timeChart = document.getElementById('timeChart');

    if (!statsContainer) return;

    // Get statistics
    const stats = window.ProductivityHub.getProductivityStats();
    const typeDistribution = window.ProductivityHub.getTaskTypeDistribution();

    // Create stats cards
    createStatsCards(stats, statsContainer);

    // Create task type distribution chart
    if (taskTypeChart && Object.keys(typeDistribution).length > 0) {
      createBarChart(typeDistribution, taskTypeChart);
    }

    // Create status distribution pie chart
    if (statusChart) {
      const statusData = {
        'Completed': stats.completed,
        'In Progress': stats.inProgress,
        'Pending': stats.pending
      };
      createPieChart(statusData, statusChart, {
        colors: ['#28a745', '#ffc107', '#6c757d']
      });
    }

    // Create time tracking summary
    if (timeChart) {
      const tasks = window.ProductivityHub.getTasks();
      const timeByOwner = {};

      tasks.forEach(task => {
        const owner = task.owner || 'Unknown';
        const minutes = window.ProductivityHub.parseTimeString(task.timeTracking);
        timeByOwner[owner] = (timeByOwner[owner] || 0) + minutes;
      });

      // Convert minutes to hours for display
      const timeByOwnerHours = {};
      Object.entries(timeByOwner).forEach(([owner, minutes]) => {
        timeByOwnerHours[owner] = Math.round(minutes / 60 * 10) / 10; // Round to 1 decimal
      });

      if (Object.keys(timeByOwnerHours).length > 0) {
        createBarChart(timeByOwnerHours, timeChart, {
          colors: ['#007bff', '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14']
        });
      }
    }
  }

  /**
   * Generate productivity report
   */
  function generateProductivityReport() {
    const stats = window.ProductivityHub.getProductivityStats();
    const tasks = window.ProductivityHub.getTasks();

    let report = '=== PRODUCTIVITY REPORT ===\n\n';
    report += `Generated: ${new Date().toLocaleString()}\n\n`;
    
    report += '--- SUMMARY ---\n';
    report += `Total Tasks: ${stats.total}\n`;
    report += `Completed: ${stats.completed} (${stats.total > 0 ? Math.round(stats.completed/stats.total*100) : 0}%)\n`;
    report += `In Progress: ${stats.inProgress}\n`;
    report += `Pending: ${stats.pending}\n`;
    report += `Overdue: ${stats.overdue}\n`;
    report += `Average Progress: ${stats.averageProgress}%\n`;
    report += `Total Time Tracked: ${window.ProductivityHub.formatTimeString(stats.totalTime)}\n\n`;

    report += '--- TASK BREAKDOWN ---\n';
    const typeDistribution = window.ProductivityHub.getTaskTypeDistribution();
    Object.entries(typeDistribution).forEach(([type, count]) => {
      report += `${type}: ${count}\n`;
    });

    report += '\n--- RECENT TASKS ---\n';
    tasks.slice(0, 10).forEach((task, index) => {
      report += `${index + 1}. ${task.title} - ${task.status} (${task.progress}%)\n`;
    });

    return report;
  }

  /**
   * Export analytics report
   */
  function exportAnalyticsReport() {
    const report = generateProductivityReport();
    
    const blob = new Blob([report], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `productivity_report_${new Date().toISOString().split('T')[0]}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);

    window.ProductivityHub.showNotification('Report exported successfully!', 'success');
  }

  // ===== INITIALIZATION =====

  document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the analytics page
    if (document.getElementById('statsCards')) {
      renderAnalyticsDashboard();

      // Add export button if it exists
      const exportBtn = document.getElementById('exportReportBtn');
      if (exportBtn) {
        exportBtn.addEventListener('click', exportAnalyticsReport);
      }

      // Listen for task updates
      window.addEventListener('tasksUpdated', renderAnalyticsDashboard);
      window.addEventListener('storage', (e) => {
        if (e.key === 'tasks' || e.key === 'tasks_last_update') {
          renderAnalyticsDashboard();
        }
      });
    }
  });

  // Export functions
  window.Analytics = {
    renderAnalyticsDashboard,
    createBarChart,
    createPieChart,
    createStatsCards,
    generateProductivityReport,
    exportAnalyticsReport
  };

})();

