<template>
  <div class="login-container">
    <h1>Login to AMW</h1>
    <form @submit.prevent="handleLogin">
      <div>
        <label>Email Address</label>
        <input v-model="email" type="email" placeholder="artist@gallery.com" required />
      </div>
      <div>
        <label>Password</label>
        <input v-model="password" type="password" placeholder="••••••••" required />
      </div>
      <button type="submit">Enter Gallery</button>
    </form>
    <p>
      Don't have an account? <router-link to="/register">Sign up</router-link>
    </p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post('http://localhost:8000/api/login', {
          email: this.email,
          password: this.password,
        });
        const token = response.data.token;
        localStorage.setItem('authToken', token); // Guardamos el token en el localStorage
        this.$router.push('/dashboard'); // Redirige a la vista principal (o lo que sea)
      } catch (error) {
        console.error('Error logging in:', error);
      }
    },
  },
};
</script>