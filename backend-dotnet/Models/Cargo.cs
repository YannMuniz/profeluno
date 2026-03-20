namespace backend_dotnet.Models
{
    public class Cargo
    {
        public int Id { get; set; }
        public string NomeCargo { get; set; }
        public DateTime CreatedAt { get; set; }
        public DateTime UpdatedAt { get; set; }

        public virtual ICollection<User> Users { get; set; } = new List<User>();
    }
}
