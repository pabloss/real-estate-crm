import { useEffect, useState, useCallback } from 'react';
import axios from 'axios';

function Dashboard() {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  const fetchDashboardData = useCallback(() => {
    setLoading(true);
    axios.get('/api/dashboard')
        .then(response => {
          setStats(response.data);
          setLoading(false);
        })
        .catch(error => {
          console.error("Error fetching dashboard data:", error);
          setLoading(false);
        });
  }, []);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  if (loading) {
    return <div>Loading dashboard...</div>;
  }

  if (!stats) {
    return <div>Could not load dashboard data.</div>;
  }

  return (
    <div style={{ padding: '2rem' }}>
      <h1>Analytical Dashboard</h1>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '1rem' }}>
        <div style={{ border: '1px solid #ccc', padding: '1rem', borderRadius: '8px' }}>
          <h2>Total Properties</h2>
          <p style={{ fontSize: '2rem', fontWeight: 'bold' }}>{stats.totalProperties}</p>
        </div>
        <div style={{ border: '1px solid #ccc', padding: '1rem', borderRadius: '8px' }}>
          <h2>Total Leads</h2>
          <p style={{ fontSize: '2rem', fontWeight: 'bold' }}>{stats.totalLeads}</p>
        </div>
        <div style={{ border: '1px solid #ccc', padding: '1rem', borderRadius: '8px' }}>
          <h2>Leads by Status</h2>
          <ul style={{ listStyleType: 'none', padding: 0 }}>
            {Object.entries(stats.leadsByStatus).map(([status, count]) => (
              <li key={status} style={{ display: 'flex', justifyContent: 'space-between' }}>
                <span style={{ textTransform: 'capitalize' }}>{status}</span>
                <strong>{count}</strong>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
}

export default Dashboard;
