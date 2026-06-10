import { CssBaseline, ThemeProvider, createTheme } from '@mui/material';
import PropertyList from './components/PropertyList';

// Prosty, nowoczesny motyw bazowy
const theme = createTheme({
  palette: {
    mode: 'light',
    primary: {
      main: '#1976d2',
    },
    background: {
      default: '#f5f5f5',
    },
  },
});

function App() {
  return (
      <ThemeProvider theme={theme}>
        {/* CssBaseline resetuje style przeglądarki na rzecz spójnego wyglądu MUI */}
        <CssBaseline />
        <PropertyList />
      </ThemeProvider>
  );
}

export default App;